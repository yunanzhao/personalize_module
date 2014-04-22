<?php

/**
 * @file
 * Contains \Drupal\personalize\AgentFormController.
 */

namespace Drupal\personalize;

use Drupal\personalize\Plugin\Type\AgentManager;
use Drupal\Core\Entity\EntityFormController;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides form controller for block instance forms.
 */
class AgentFormController extends EntityFormController {

  /**
   * The block entity.
   *
   * @var \Drupal\personalize\AgentInterface
   */
  protected $entity;

  /**
   * Constructs an AgentFormController object.
   */
  public function __construct(AgentManager $agent_manager) {
    $this->agentManager = $agent_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.personalize_agent')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, array &$form_state) {
    $entity = $this->entity;
    $form['#tree'] = TRUE;
    //$form['settings'] = $entity->getPlugin()->buildConfigurationForm(array(), $form_state);
    $agent_types = $this->agentManager->getDefinitions();
    $agent_type_options = $agent_type_form_options = array();
    foreach ($agent_types as $name => $info) {
      // Add this option to the options for the "agent type" dropdown.
      $agent_type_options[$name] = $name;
      // Get the agent type's options form elements
      $agent_type_form_options[$name] = call_user_func_array(array($info['class'], 'optionsForm'), array($entity));
    }
    if (empty($agent_type_options)) {
      drupal_set_message(t('You don\'t have any campaign types enabled. Please enable the personalize_target module or another module that provides a campaign type'));
      return array();
    }
    ksort($agent_type_options);
    if (count($agent_type_options) < 2) {
      $form['agent_type'] = array(
        '#type' => 'value',
        '#value' => key($agent_type_options),
      );
    }
    else {
      $form['agent_type'] = array(
        '#type' => 'select',
        '#title' => t('Agent Type'),
        '#options' => $agent_type_options,
        '#default_value' => isset($entity->agent_type) ? $entity->agent_type : '',
        '#description' => t('Choose which type of agent to create.'),
        '#weight' => -7,
      );
    }

    $form['label'] = array(
      '#title' => t('Title'),
      '#description' => t('The administrative title of this agent.'),
      '#type' => 'textfield',
      '#default_value' => $entity->get('label'),
      '#weight' => -9,
      '#required' => TRUE,
    );
    // If creating a new agent, calculate a safe default machine name.
    $form['id'] = array(
      '#type' => 'machine_name',
      '#maxlength' => 64,
      '#description' => $this->t('A unique name for this agent. Must be alpha-numeric and hyphen separated.'),
      '#default_value' =>  $entity->id(),
      '#machine_name' => array(
        'exists' => 'personalize_agent_load',
        'source' => array('label'),
        'replace_pattern' => '[^a-z0-9-]+',
        'replace' => '-',
      ),
      '#required' => TRUE,
      '#disabled' => !$entity->isNew(),
    );

    $form['actions'] = array('#type' => 'actions');
    $form['actions']['submit_form'] = array(
      '#type' => 'submit',
      '#value' => t('Save'),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submit(array $form, array &$form_state) {
    parent::submit($form, $form_state);

    $agent_type = $form_state['values']['agent_type'];
    $data = isset($form_state['values']['options'][$agent_type]) ? $form_state['values']['options'][$agent_type] : array();
    $entity = $this->entity;
    $entity->set('data', $data);
    $entity->save();

  }


}
