<?php

/**
 * @file
 * Definition of Drupal\personalize\Entity\Agent.
 */

namespace Drupal\personalize\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Entity\EntityStorageControllerInterface;
use Drupal\views\Views;
use Drupal\views_ui\ViewUI;
use Drupal\views\ViewStorageInterface;
use Drupal\views\ViewExecutable;

/**
 * Defines an agent configuration entity class.
 *
 * @ConfigEntityType(
 *   id = "personalize_agent",
 *   label = @Translation("Campaign"),
 *   controllers = {
 *     "form" = {
 *       "add" = "Drupal\personalize\Form\AgentAddForm",
 *       "edit" = "Drupal\personalize\Form\AgentEditForm",
 *       "delete" = "Drupal\personalize\Form\AgentDeleteForm",
 *     },
 *     "list_builder" = "Drupal\personalize\AgentListBuilder",
 *   },
 *   admin_permission = "administer personalization",
 *   config_prefix = "agent",
 *   entity_keys = {
 *     "id" = "name",
 *     "label" = "label"
 *   },
 *   links = {
 *     "edit-form" = "personalize.agent_edit",
 *     "delete-form" = "personalize.agent_delete"
 *   }
 * )
 */
class Agent extends ConfigEntityBase {

  /**
   * The name of the agent.
   *
   * @var string
   */
  public $name;

  /**
   * The agent label.
   *
   * @var string
   */
  public $label;

  /**
   * The plugin ID of the action.
   *
   * @var string
   */
  protected $plugin;

  protected $pluginConfigKey = 'configuration';

  protected $configuration;

  protected $mvt_enabled;

  /**
   * The plugin instance ID.
   *
   * @var string
   */
  protected $agent_type;

  /**
   * {@inheritdoc}
   */
  public function toArray() {
    $properties = parent::toArray();
    $names = array(
      'agent_type',
      'configuration',
    );
    foreach ($names as $name) {
      $properties[$name] = $this->get($name);
    }
    return $properties;
  }

  /**
   * Overrides Drupal\Core\Entity\Entity::id().
   */
  public function id() {
    return $this->name;
  }

  public function getPlugin() {
    return $this->get('plugin');
  }

  public function getConfiguration() {
    return $this->get('configuration');
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name');
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
  }
}
