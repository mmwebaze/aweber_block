<?php

namespace Drupal\aweber_block\Controller;

use Drupal\aweber_block\Service\AweberAuthenticationInterface;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AweberBlockController.
 */
class AweberBlockController extends ControllerBase {

  /**
   * The Authenthocation service.
   *
   * @var AweberAuthenticationInterface
   */
  protected $authenticationService;

  public function __construct(AweberAuthenticationInterface $authenticationService) {
    $this->authenticationService = $authenticationService;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('aweber_block.authentication')
      //$container->get('aweber_block.manager'),
      //$container->get('messenger')
    );
  }

  /**
   * The callback route.
   *
   * @param Request $request
   *   The request object.
   *
   * @return array
   *   The render array or response.
   */
  public function getCode(Request $request) {
    $message = 'Access hasn\'t been granted';

    $code = $request->query->get('code');

    if ($code) {
      $response = $this->authenticationService->getAccessToken($code);
      $accessTokenResponse = json_decode((string) $response);

      if ($accessTokenResponse){
        $this->authenticationService->saveAccessToken($accessTokenResponse);

        $message = 'Access has been granted.';
        //redirect to config page
      }
    }
    return array(
      '#type' => 'markup',
      '#markup' => '<div>'.$message.'</div>'
    );
  }
  /**
   * Gets application authorization link.
   *
   * @return mixed
   *
   *   A render array or response.
   */
  public function getAuthorization() {
    $url = $this->authenticationService->buildAuthorizationUrl();

    if (!$url) {
      return $this->redirect('aweber_block.aweber_block_config_form');
    }

    return [
      '#type' => 'markup',
      '#markup' => '<a href=' . $url . ' target="_blank">Click to connect to Aweber to authorize App</a>',
    ];
  }

  /**
   * @return array
   */
  public function index(){

    return array(
      '#theme' => 'aweber_block',
    );
  }
}
