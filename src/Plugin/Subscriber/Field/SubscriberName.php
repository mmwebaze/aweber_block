<?php

namespace Drupal\aweber_block\Plugin\Subscriber\Field;

use Drupal\aweber_block\Plugin\SubscriberFieldInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Class for sending a code by email.
 *
 * @AweberField(
 *   id = "name",
 *   label = @Translation("Name"),
 *   description = @Translation("Subscriber Name"),
 * )
 */
class SubscriberName implements SubscriberFieldInterface {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function field() {
    return array(
      '#title' => $this->t('Name'),
      '#type' => 'textfield',
      '#maxlength' => 60,
      '#required' => TRUE,
    );
  }
}
