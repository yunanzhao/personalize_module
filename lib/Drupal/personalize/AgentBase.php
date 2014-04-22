<?php

/**
 * @file
 * Contains \Drupal\block\BlockBase.
 */

namespace Drupal\personalize;

use Drupal\Core\Plugin\PluginBase;
use Drupal\Component\Utility\Unicode;
use Drupal\Core\Language\Language;
use Drupal\Core\Session\AccountInterface;

/**
 * Abstract base class for agents, providing default implementations of some methods.
 */
abstract class AgentBase extends PluginBase implements AgentInterface {

  public $id;
  /**
   * The machine_name of the agent.
   *
   * @var string
   */
  protected $machineName;

  /**
   * The title of the agent.
   *
   * @var string
   */
  protected $title;

  /**
   * An array of data describing the agent.
   *
   * @var array
   */
  protected $data;

  /**
   * {@inheritdoc}
   */
  public function access(AccountInterface $account) {
    return TRUE;
  }

  /**
   * {@inheritdoc}
   *
   * Creates a generic configuration form for all agent types. Individual
   * block plugins can add elements to this form by overriding
   * BlockBase::blockForm(). Most block plugins should not override this
   * method unless they need to alter the generic form elements.
   *
   * @see \Drupal\block\BlockBase::blockForm()
   */
  public function buildConfigurationForm(array $form, array &$form_state) {
    $form['stuff'] = array(
      '#title' => 'ohai',
      '#type' => 'textfield',
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   *
   * Most block plugins should not override this method. To add validation
   * for a specific block type, override BlockBase::blockValdiate().
   *
   * @see \Drupal\block\BlockBase::blockValidate()
   */
  public function validateConfigurationForm(array &$form, array &$form_state) {
    $this->blockValidate($form, $form_state);
  }

  /**
   * {@inheritdoc}
   *
   * Most block plugins should not override this method. To add submission
   * handling for a specific block type, override BlockBase::blockSubmit().
   *
   * @see \Drupal\block\BlockBase::blockSubmit()
   */
  public function submitConfigurationForm(array &$form, array &$form_state) {
    // Process the block's submission handling if no errors occurred only.
    if (!form_get_errors($form_state)) {
      $this->configuration['label'] = $form_state['values']['label'];
      $this->configuration['label_display'] = $form_state['values']['label_display'];
      $this->configuration['module'] = $form_state['values']['module'];
      $this->blockSubmit($form, $form_state);
    }
  }

}
