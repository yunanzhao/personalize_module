<?php

/**
 * @file
 * Contains \Drupal\personalize\Form\AgentDeleteForm.
 */

namespace Drupal\personalize\Form;

use Drupal\Core\Entity\EntityConfirmFormBase;

/**
 * Creates a form to delete an image style.
 */
class AgentDeleteForm extends EntityConfirmFormBase {

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Delete %agent', array('%agent' => $this->entity->label()));
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelRoute() {
    return array(
      'route_name' => 'personalize.agent_list',
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->t('This will permanently delete the agent and you will no longer see reports.');
  }

  /**
   * {@inheritdoc}
   */
  public function submit(array $form, array &$form_state) {
    $this->entity->delete();
    drupal_set_message($this->t('Agent %name was deleted.', array('%name' => $this->entity->label())));
    $form_state['redirect_route']['route_name'] = 'personalize.agent_list';
  }

}
