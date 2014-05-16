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
   * Returns an array of assets that can be set as the #attached property
   * of an element in a render array.
   *
   * @return array
   *   An array of assets keyed by type, e.g. 'js', 'css', 'library'.
   */
  public function getAssets();

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
