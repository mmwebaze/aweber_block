<?php

namespace Drupal\aweber_block\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines an Aweber Subscriber Field annotation object.
 *
 * @Annotation
 */
class AweberField extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The human-readable name of the Aweber Subscriber Field.
   *
   * @ingroup plugin_translatable
   *
   * @var \Drupal\Core\Annotation\Translation
   */
  public $title;

  /**
   * The description shown to users.
   *
   * @ingroup plugin_translatable
   *
   * @var \Drupal\Core\Annotation\Translation
   */
  public $description;
}
