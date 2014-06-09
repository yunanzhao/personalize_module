<?php

namespace Drupal\personalize;

use Drupal\Core\Plugin\PluginFormInterface;

/**
 * Interface that all personalize option set type plugins must implement.
 */
interface OptionSetInterface extends PluginFormInterface {

  public function render();

}
