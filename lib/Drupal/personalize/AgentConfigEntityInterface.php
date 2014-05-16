<?php

/**
 * @file
 * Contains \Drupal\personalize\AgentConfigEntityInterface.
 */

namespace Drupal\personalize;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface defining a action entity.
 */
interface AgentConfigEntityInterface extends ConfigEntityInterface {
  /**
   * Returns the agent plugin.
   *
   * @return \Drupal\personalize\AgentInterface
   */
  public function getPlugin();

}
