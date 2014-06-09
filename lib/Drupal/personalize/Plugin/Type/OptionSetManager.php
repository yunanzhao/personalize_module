<?php

/**
 * @file
 * Contains \Drupal\personalize\Plugin\Type\OptionSetManager.
 */

namespace Drupal\personalize\Plugin\Type;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Language\LanguageManager;
use Drupal\Core\Plugin\DefaultPluginManager;

/**
 * Manages discovery and instantiation of agent plugins.
 */
class OptionSetManager extends DefaultPluginManager {

  /**
   * Constructs a new \Drupal\personalize\Plugin\Type\OptionSetManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Language\LanguageManager $language_manager
   *   The language manager.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, LanguageManager $language_manager, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/OptionSet', $namespaces, $module_handler, 'Drupal\personalize\Annotation\OptionSet');

    $this->alterInfo('personalize_option_set');
    $this->setCacheBackend($cache_backend, $language_manager, 'option_set_plugins');
  }

}
