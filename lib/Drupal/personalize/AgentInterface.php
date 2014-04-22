<?php
/**
 * @file
 * Provides interfaces and base classes for Personalize plugins.
 */

namespace Drupal\personalize;

use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Component\Plugin\ConfigurablePluginInterface;
use Drupal\Core\Plugin\PluginFormInterface;

/**
 * Interface that all personalize agent type plugins must implement.
 */
interface AgentInterface extends PluginFormInterface {


  /**
   * Sends a goal to an agent.
   *
   * @param string $goal_name
   *   The name of the goal being sent.
   * @param int $value
   *   (optional) The value of the goal being sent.
   */
  public function sendGoal($goal_name, $value = NULL);

  /**
   * Returns an array of assets that can be set as the #attached property
   * of an element in a render array.
   *
   * @return array
   *   An array of assets keyed by type, e.g. 'js', 'css', 'library'.
   */
  public function getAssets();

  /**
   * Returns the options form for configuring this type of agent.
   *
   * @param $agent_data
   *   The agent being edited, if it's an edit form.
   *
   * @return array
   *   A FAPI array.
   */
  public static function optionsForm($agent_data);

  /**
   * A method to call once an agent has been saved.
   *
   * @param stdClass $agent_data
   *   Object representing the agent that has just been saved.
   * @param boolean $new
   *   Whether this is a newly created agent.
   */
  public static function saveCallback($agent_data, $new = TRUE);
}
