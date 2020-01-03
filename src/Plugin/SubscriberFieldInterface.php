<?php

namespace Drupal\aweber_block\Plugin;

/**
 * Defines the required interface for all Aweber Subscriber Field plugins.
 *
 * @package Drupal\aweber_block\Plugin
 */
interface SubscriberFieldInterface {

  /**
   * Returns the form element.
   *
   * @return array
   *   Array defining the form element.
   */
  public function field();
}
