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
 * @EntityType(
 *   id = "personalize_agent",
 *   label = @Translation("Campaign"),
 *   module = "personalize",
 *   controllers = {
 *     "storage" = "Drupal\Core\Config\Entity\ConfigEntityStorage",
 *     "access" = "Drupal\Core\Entity\EntityAccessController",
 *     "list_builder" = "Drupal\personalize\PersonalizeAgentListController",
 *     "form" = {
 *       "default" = "Drupal\personalize\AgentFormController",
 *     }
 *   },
 *   admin_permission = "administer personalization",
 *   config_prefix = "personalize.agent",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid",
 *   },
 *   links = {
 *     "edit-form" = "personalize.agent_edit"
 *   }
 * )
 */
class Agent extends ConfigEntityBase {
  /**
   * The ID of the block.
   *
   * @var string
   */
  public $id;

  protected $data;

  protected $mvt_enabled;

  /**
   * The label of the view.
   */
  protected $label;

  /**
   * The plugin instance ID.
   *
   * @var string
   */
  protected $agent_type;

  /**
   * Overrides \Drupal\Core\Config\Entity\ConfigEntityBase::getExportProperties();
   */
  public function getExportProperties() {
    $names = array(
      'agent_type',
      'id',
      'label',
      'data',
      'mvt_enabled',
    );
    $properties = array();
    foreach ($names as $name) {
      $properties[$name] = $this->get($name);
    }
    return $properties;
  }

}
