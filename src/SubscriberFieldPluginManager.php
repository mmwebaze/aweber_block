<?php

namespace Drupal\aweber_block;


use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;

/**
 * The subscriber field plugin manager.
 */
class SubscriberFieldPluginManager extends DefaultPluginManager {

  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler, $plugin_definition_annotation_name = 'Drupal\Component\Annotation\Plugin') {
    parent::__construct('Plugin/Subscriber/Field', $namespaces, $module_handler, 'Drupal\aweber_block\Plugin\SubscriberFieldInterface', 'Drupal\aweber_block\Annotation\AweberField');
    $this->alterInfo('aweber_block_subscriber_field_info');
    $this->setCacheBackend($cache_backend, 'aweber_block_subscriber_field');
  }
}
