<?php

namespace Drupal\personalize_elements\Plugin\OptionSet;

use Drupal\personalize\OptionSetBase;

/**
 * Defines a Personalize Elements Option Set.
 *
 * @OptionSet(
 *   id = "personalize_elements"
 * )
 */
class PersonalizeElementsOptionSet extends OptionSetBase {

  public function render() {
    return array();
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
    $form['moar_stuff'] = array(
      '#title' => 'err',
      '#type' => 'textfield',
      '#default_value' => isset($this->configuration['moar_stuff']) ? $this->configuration['moar_stuff'] : ''
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
/*    // Process the block's submission handling if no errors occurred only.
    if (!form_get_errors($form_state)) {
      $this->configuration['label'] = $form_state['values']['label'];
      $this->configuration['label_display'] = $form_state['values']['label_display'];
      $this->configuration['module'] = $form_state['values']['module'];
      $this->blockSubmit($form, $form_state);
    }*/
  }

}
