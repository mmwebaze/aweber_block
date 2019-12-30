<?php

namespace Drupal\aweber_block\Service;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ClientException;

class AweberManager implements AweberServiceInterface {
  /**
   * The base api url.
   *
   * @var string
   */
  protected $baseUrl;
  /**
   * @var ClientInterface
   */
  protected $httpClient;
  /**
   * The unexpired access token.
   *
   * @var string
   */
  protected $accessToken;
  /**
   * @var int
   */
  protected $accountId;

  public function __construct(AweberAuthenticationInterface $aweberAuthentication) {
    $this->baseUrl = $aweberAuthentication->getBaseUrl();
    $this->httpClient = $aweberAuthentication->getHttpClient();
    $this->accessToken = $aweberAuthentication->getStoredAccessToken();
    $this->accountId = $aweberAuthentication->getAccountId();
  }
  /**
   * {@inheritdoc}
   */
  public function accounts(){
    $accounts = array();
    $endPoint = "{$this->baseUrl}/accounts";
    $response = $this->get($endPoint);

    if (!is_null($response)){
      $results = json_decode($response->getBody(), true);
      $accounts = array_merge($results['entries'], $accounts);
    }

    return $accounts;
  }
  /**
   * {@inheritdoc}
   */
  public function lists(int $accountID){
    $lists = array();
    $endPoint = "{$this->baseUrl}/accounts/{$accountID}/lists";
    $response = $this->get($endPoint);
    if (!is_null($response)){
      $results = json_decode($response->getBody(), true);
      $entries = $results['entries'];
      foreach ($entries as $entry){
        $lists[$entry['id']] = $entry['name'];
      }
    }

    return $lists;
  }

  /**
   * {@inheritdoc}
   */
  public function addSubscribers(int $listID, array $params){
    $endPoint = "{$this->baseUrl}/accounts/{$this->accountId}/lists/{$listID}/subscribers";
    return $this->post($endPoint, $params);
  }

  /**
   * {@inheritdoc}
   */
  public function checkSubscriberExistsByEmail($email, int $listId) {
    $endPoint = "{$this->baseUrl}/accounts/{$this->accountId}/lists/{$listId}/subscribers?ws.op=find&email={$email}";
    $response = $this->get($endPoint);
    if (!is_null($response)){
      $results = json_decode($response->getBody(), true);
      $entries = $results['entries'];
      if (empty($entries)){

        return TRUE;
      }
      return FALSE;
    }
    return FALSE;
  }

  private function get($endPoint) {
    try {
      $response = $this->httpClient->request('GET', $endPoint, [
        'headers' => ['Authorization' => 'Bearer ' . $this->accessToken]
      ]);

      return $response;
    } catch (GuzzleException $e) {
      \Drupal::logger('Aweber_block')->notice($e->getMessage());
      return null;
    }
  }

  private function post($endPoint, $payload){
    try{
      $this->httpClient->post($endPoint,[
        'headers' => ['Authorization' => 'Bearer ' . $this->accessToken],
        'json' => $payload,
      ]);
      return TRUE;
    }
    catch (ClientException $e){
      \Drupal::logger('Aweber_block')->notice($e->getMessage());
      return FALSE;
    }
  }
}
