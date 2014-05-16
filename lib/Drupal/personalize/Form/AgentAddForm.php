<?php

/**
 * @file
 * Contains \Drupal\personalize\Form\AgentAddForm.
 */

namespace Drupal\personalize\Form;

/**
 * Provides form controller for block instance forms.
 */
class AgentAddForm extends AgentFormBase {

  /**
   * {@inheritdoc}
   */
  public function save(array $form, array &$form_state) {
    parent::save($form, $form_state);
    drupal_set_message($this->t('Agent %name was created.', array('%name' => $this->entity->label())));
  }

  /**
   * {@inheritdoc}
   */
  public function actions(array $form, array &$form_state) {
    $actions = parent::actions($form, $form_state);
    $actions['submit']['#value'] = $this->t('Create new agent');

    return $actions;
  }

}
