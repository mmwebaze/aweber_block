<?php


namespace Drupal\aweber_block\Service;

use Drupal\aweber_block\AweberScopes;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Component\Datetime\TimeInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;

/**
 * The Aweber Authentication Service Manager.
 *
 * @package Drupal\aweber_block\Service
 */
class AweberAuthentication implements AweberAuthenticationInterface {
  /**
   * The configuration service.
   *
   * @var ConfigFactoryInterface
   */
  protected $configFactory;
  /**
   * Http Client service.
   *
   * @var ClientInterface
   */
  protected $client;
  /**
   * Default Aweber scopes.
   *
   * @var
   */
  protected $scopes;

  /**
   * The Time Service.
   *
   * @var TimeInterface
   */
  protected $time;

  public function __construct(ConfigFactoryInterface $configFactory, ClientInterface $client, TimeInterface $time) {
    $this->client = $client;
    $this->configFactory = $configFactory;
    $this->time = $time;
  }

  public function buildAuthorizationUrl() {
    $config = $this->configFactory->get('aweber_block.aweberblockconfig');
    $selectedScopes = $config->get('scopes');
    $scopes = AweberScopes::SCOPES;

    $sco = array();
    foreach ($selectedScopes as $selectedScope){
      if ($selectedScope !== 0)
        array_push($sco, $scopes[$selectedScope]);
    }
    //@todo state value 1234 should be replaced with randomly generated state_token
    //@todo implement code_challenge

    $authorizeQuery = array(
      "response_type" => "code",
      "client_id" => $config->get('client_id'),
      "redirect_uri" => $config->get('redirect_uri'),
      "scope" => implode(" ",$sco),
      "state" => '1234',
      //"code_challenge" => $codeChallenge,
      //"code_challenge_method" => "S256"
    );

    $url = $config->get('auth_request_url') . "?" . http_build_query($authorizeQuery) . "\n";

    return $url;
  }
  /**
   * {@inheritdoc}
   */
  public function getAccessToken($code) {
    $config = $this->configFactory->get('aweber_block.aweberblockconfig');
    $clientId = $config->get('client_id');
    $clientSecret = $config->get('client_secret');
    $callBack = $config->get('redirect_uri');

    //@todo use the value stored in config object.
    $url = "https://auth.aweber.com/oauth2/token";
    try {
      $response = $this->client->request('POST', $url, [
        'auth' => [$clientId, $clientSecret],
        'query' => [
          'grant_type' => 'authorization_code',
          'code' => $code,
          'redirect_uri' => $callBack
        ]
      ]);

      return $response->getBody();
    } catch (GuzzleException $e) {
      \Drupal::logger('aweber_block')->notice($e->getMessage());
      return null;
    }
  }
  /**
   * {@inheritdoc}
   */
  public function refreshAccessToken() {
    $config = $this->configFactory->get('aweber_block.aweberblockconfig');
    $clientId = $config->get('client_id');
    $clientSecret = $config->get('client_secret');
    $refreshToken = $config->get('refresh_token');

    $url = "https://auth.aweber.com/oauth2/token";
    try {
      $response = $this->client->request('POST', $url, [
        'auth' => [$clientId, $clientSecret],
        'query' => [
          'grant_type' => 'refresh_token',
          'refresh_token' => $refreshToken,
        ]
      ]);

      return json_decode((string) $response->getBody());
    } catch (GuzzleException $e) {
      \Drupal::logger('aweber_block')->notice($e->getMessage());
      return null;
    }
  }
  /**
   * {@inheritdoc}
   */
  public function saveAccessToken($tokenObject) {
    if ($tokenObject) {
      $config = $this->configFactory->getEditable('aweber_block.aweberblockconfig');
      $config->set('auth_token', $tokenObject->access_token)
        ->set('refresh_token', $tokenObject->refresh_token)
        ->set('expires_in', $tokenObject->expires_in + $this->time->getCurrentTime())->save();
    }
  }
  /**
   * {@inheritdoc}
   */
  public function getStoredAccessToken() {
    //@todo probably want to check if token is still valid. if invalid, refresh token return refreshed token and save it as well.
    $timestamp = $this->time->getCurrentTime();
    $expiresIn = $this->configFactory->get('aweber_block.aweberblockconfig')->get('expires_in');

    //Refresh token
    if ($timestamp > $expiresIn) {
      $tokenObject = $this->refreshAccessToken();
      $this->saveAccessToken($tokenObject);
      return $tokenObject->access_token;
    }

    return $this->configFactory->get('aweber_block.aweberblockconfig')->get('auth_token');
  }
  /**
   * {@inheritdoc}
   */
  public function getHttpClient() {
    return $this->client;
  }

  /**
   * {@inheritdoc}
   */
  public function getBaseUrl() {

    return $this->configFactory->get('aweber_block.aweberblockconfig')->get('base_url');
  }

  /**
   * {@inheritdoc}
   */
  public function getAccountId(){

    return $this->configFactory->get('aweber_block.aweberblockconfig')->get('aweber_account_id');
  }
}
