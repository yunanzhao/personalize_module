<?php

/**
 * @file
 * Contains \Drupal\personalize\AgentFormController.
 */

namespace Drupal\personalize;

use Drupal\personalize\Plugin\Type\AgentManager;
use Drupal\Core\Entity\EntityFormController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityStorageInterface;

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

  protected $storage;

  /**
   * Constructs an AgentFormController object.
   */
  public function __construct(AgentManager $agent_manager, EntityStorageInterface $storage) {
    $this->agentManager = $agent_manager;
    $this->storage = $storage;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.personalize_agent'),
      $container->get('entity.manager')->getStorage('personalize_agent')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, array &$form_state) {
    $form['#tree'] = TRUE;
    $form = $this->buildBasicForm();
    $form['actions'] = array('#type' => 'actions');
    $form['actions']['submit_form'] = array(
      '#type' => 'submit',
      '#value' => t('Save'),
    );
    return $form;
  }

  protected function buildBasicForm($include_machine_name_field = TRUE, $parent_array = NULL) {
    $form = array();
    $form['agent_basic_info'] = array(
      '#tree' => TRUE
    );

    $form['#agent'] = $this->entity;
    // Make sure we have at least one agent type available.
    $agent_types = $this->agentManager->getDefinitions();
    $agent_type_options = $agent_type_form_options = array();
    foreach ($agent_types as $name => $info) {
      // Add this option to the options for the "agent type" dropdown.
      $agent_type_options[$name] = $name;
      // Get the agent type's options form elements
      $agent_type_form_options[$name] = call_user_func_array(array($info['class'], 'optionsForm'), array($this->entity));
    }
    if (empty($agent_type_options)) {
      drupal_set_message(t('You don\'t have any campaign types enabled. Please enable the personalize_target module or another module that provides a campaign type'));
      return array();
    }
    ksort($agent_type_options);

    $form['agent_basic_info']['label'] = array(
      '#title' => t('Title'),
      '#description' => t('The administrative title of this agent.'),
      '#type' => 'textfield',
      '#default_value' => $this->entity->get('label'),
      '#weight' => -9,
      '#required' => TRUE,
    );
    if ($include_machine_name_field) {
      // Define the parent array to the source input.
      if (empty($parent_array)) {
        $parent_array = array();
      }
      $parent_array[] = 'agent_basic_info';
      $parent_array[] = 'label';
/*      $form['agent_basic_info']['machine_name'] = array(
        '#type' => 'machine_name',
        '#maxlength' => PERSONALIZE_MACHINE_NAME_MAXLENGTH,
        '#machine_name' => array(
          'exists' => 'personalize_agent_machine_name_exists',
          'source' => $parent_array,
          'replace_pattern' => PERSONALIZE_MACHINE_NAME_REPLACE_PATTERN,
          'replace' => '-',
        ),
        '#description' => t('A unique machine-readable name for this agent. It must only contain lowercase letters, numbers, and hyphens.'),
        '#weight' => -8,
      );*/
/*      if (!empty($this->entity->getId())) {
        $form['agent_basic_info']['machine_name']['#default_value'] = $agent_data->machine_name;
        $form['agent_basic_info']['machine_name']['#disabled'] = TRUE;
        $form['agent_basic_info']['machine_name']['#value'] = $agent_data->machine_name;
      }*/
      // If creating a new agent, calculate a safe default machine name.
      $form['agent_basic_info']['id'] = array(
        '#type' => 'machine_name',
        '#maxlength' => 64,
        '#description' => $this->t('A unique name for this agent. Must be alpha-numeric and hyphen separated.'),
        '#default_value' =>  $this->entity->id(),
        '#machine_name' => array(
          'exists' => array($this->storage, 'load'),
          'source' => $parent_array,
          'replace_pattern' => '[^a-z0-9-]+',
          'replace' => '-',
        ),
        '#required' => TRUE,
        '#disabled' => !$this->entity->isNew(),
      );
    }

    if (!empty($blarg)) {
      // It is not possible to change the type of an agent.
      $form['agent_basic_info']['agent_type'] = array(
        '#type' => 'value',
        '#value' => $this->entity->plugin,
      );
    }
    elseif (count($agent_type_options) < 2) {
      // No need to show a dropdown if there's only one available plugin.
      $form['agent_basic_info']['agent_type'] = array(
        '#type' => 'hidden',
        '#value' => key($agent_type_options),
      );
    }
    else {
      $form['agent_basic_info']['agent_type'] = array(
        '#type' => 'select',
        '#title' => t('Agent Type'),
        '#options' => $agent_type_options,
        '#default_value' => '',
        '#description' => t('Choose which type of agent to create.'),
        '#weight' => -7,
      );
    }

    // Add the agent-type-specific form elements to the form, to be shown only if the
    // agent type in question is selected.
    $form['agent_basic_info']['options'] = array('#tree' => TRUE, '#weight' => -6);
    if (!empty($blarg)) {
      // If in edit mode, then only show the options for the selected agent.
      $form['agent_basic_info']['options'][$this->entity->plugin] = $agent_type_form_options[$this->entity->plugin];
    }
    else {
      // If in add mode, then show options dynamically using states.
      foreach ($agent_type_form_options as $agent_type => $options) {
        foreach ($options as &$option) {
          $option['#states'] = array('visible' => array(':input[name="agent_basic_info[agent_type]"]' => array('value' => $agent_type)));
        }
        $form['agent_basic_info']['options'][$agent_type] = $options;
      }
    }
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submit(array $form, array &$form_state) {
    parent::submit($form, $form_state);
debug($form_state['values']);
    $agent_type = $form_state['values']['agent_basic_info']['agent_type'];
    $data = isset($form_state['values']['agent_basic_info']['options'][$agent_type]) ? $form_state['values']['agent_basic_info']['options'][$agent_type] : array();
    $entity = $this->entity;
    $entity->set('id', $form_state['values']['agent_basic_info']['id']);
    $entity->set('label', $form_state['values']['agent_basic_info']['label']);
    $entity->set('agent_type', $agent_type);
    $entity->set('data', $data);
    debug($entity);
    $entity->save();

  }


}
