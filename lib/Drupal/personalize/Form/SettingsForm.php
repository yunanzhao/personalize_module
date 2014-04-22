<?php

/**
 * @file
 * Contains \Drupal\personalize\Form\SettingsForm.
 */

namespace Drupal\personalize\Form;

use Drupal\Core\Form\ConfigFormBase;

/**
 * Form builder for the admin display defaults page.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'personalize_settings_general';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, array &$form_state) {
    $form = parent::buildForm($form, $form_state);

    $config = $this->configFactory->get('personalize.settings');

    // This is not currently a fieldset but we may want it to be later,
    // so this will make it easier to change if we do.
    $form['some_setting'] =  array(
      '#type' => 'checkbox',
      '#title' => $this->t('Some setting'),
      '#default_value' => $config->get('some_setting'),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, array &$form_state) {
    $this->configFactory->get('personalize.settings')
      ->set('some_setting', $form_state['values']['some_setting'])
      ->save();
  }

}
