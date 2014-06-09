<?php

/**
 * @file
 * Contains \Drupal\personalize\Form\AgentEditForm.
 */

namespace Drupal\personalize\Form;

/**
 * Provides form controller for block instance forms.
 */
class AgentEditForm extends AgentFormBase {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, array &$form_state) {
    $agent_plugin = $this->agentManager->createInstance($this->entity->getPlugin(), $this->entity->toArray());
    $form['configuration'] = $agent_plugin->buildConfigurationForm($form, $form_state);
    return parent::form($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function actions(array $form, array &$form_state) {
    $actions = parent::actions($form, $form_state);
    $actions['submit']['#value'] = $this->t('Update agent');

    return $actions;
  }
}
