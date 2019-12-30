<?php

namespace Drupal\aweber_block\Service;

use GuzzleHttp\ClientInterface;

interface AweberAuthenticationInterface {
  public function buildAuthorizationUrl();
  public function getAccessToken($code);
  public function refreshAccessToken();

  /**
   * Saves the access token, refresh token and token expiry.
   *
   * @param $tokenObject
   *   The stdObject containing refresh_token, token_type, token_type and expires_in parameters.
   */
  public function saveAccessToken($tokenObject);
  public function getStoredAccessToken();
  /**
   * Gets the http client.
   *
   * @return ClientInterface
   *   The http client.
   */
  public function getHttpClient();

  /**
   * Gets the base url.
   *
   * @return string
   *   The base url.
   */
  public function getBaseUrl();

  /**
   * Gets the configured Aweber account ID.
   *
   * @return int
   *   The Aweber account Id.
   */
  public function getAccountId();
}
