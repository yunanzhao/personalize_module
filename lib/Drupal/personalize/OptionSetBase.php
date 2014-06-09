<?php

/**
 * @file
 * Contains \Drupal\personalize\OptionSetBase.
 */

namespace Drupal\personalize;

use Drupal\Core\Plugin\PluginBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Abstract base class for agents, providing default implementations of some methods.
 */
abstract class OptionSetBase extends PluginBase implements OptionSetInterface, ContainerFactoryPluginInterface {

  protected $id;

  /**
   * The title of the agent.
   *
   * @var string
   */
  protected $label;

  /**
   * {@inheritdoc}
   */
  public function access(AccountInterface $account) {
    return TRUE;
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    // $configuration is actually the whole entity info. Let's extract
    // what the plugin actually needs
    return new static($configuration['id'], $configuration['label'], $configuration['configuration'], $plugin_id, $plugin_definition);
  }

  /**
   * Constructs a Drupal\Component\Plugin\PluginBase object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   */
  public function __construct($osid, $os_label, array $configuration, $plugin_id, $plugin_definition) {
    $this->id = $osid;
    $this->label = $os_label;
    $this->configuration = $configuration;
    $this->pluginId = $plugin_id;
    $this->pluginDefinition = $plugin_definition;
  }

  public function getId() {
    return $this->id;
  }

  public function getLabel() {
    return $this->label;
  }
}
