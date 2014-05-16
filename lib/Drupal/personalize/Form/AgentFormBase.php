<?php

/**
 * @file
 * Contains \Drupal\personalize\Form\AgentFormBase.
 */

namespace Drupal\personalize\Form;

use Drupal\personalize\Plugin\Type\AgentManager;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityStorageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Provides form controller for block instance forms.
 */
class AgentFormBase extends EntityForm {

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
    $form += $this->buildBasicForm($form, $form_state);
    $form['actions'] = array('#type' => 'actions');
    $form['actions']['submit_form'] = array(
      '#type' => 'submit',
      '#value' => t('Save'),
    );
    return parent::form($form, $form_state);
  }

  protected function buildBasicForm($form, &$form_state) {
    // Make sure we have at least one agent type available.
    $agent_types = $this->agentManager->getDefinitions();
    $agent_type_options = array();
    foreach ($agent_types as $name => $info) {
      // Add this option to the options for the "agent type" dropdown.
      $agent_type_options[$name] = $name;
    }
    if (empty($agent_type_options)) {
      drupal_set_message(t('You don\'t have any campaign types enabled. Please enable the personalize_target module or another module that provides a campaign type'));
      return array();
    }
    ksort($agent_type_options);

    $form['label'] = array(
      '#title' => t('Title'),
      '#description' => t('The administrative title of this agent.'),
      '#type' => 'textfield',
      '#default_value' => $this->entity->get('label'),
      '#weight' => -9,
      '#required' => TRUE,
    );
    $form['name'] = array(
      '#type' => 'machine_name',
      '#maxlength' => 64,
      '#description' => $this->t('A unique name for this agent. Must be alpha-numeric and hyphen separated.'),
      '#default_value' =>  $this->entity->id(),
      '#machine_name' => array(
        'exists' => array($this->storage, 'load'),
        'source' => array('label'),
        'replace_pattern' => '[^a-z0-9-]+',
        'replace' => '-',
      ),
      '#required' => TRUE,
      '#disabled' => !$this->entity->isNew(),
    );

    if (count($agent_type_options) < 2) {
      // No need to show a dropdown if there's only one available plugin.
      $form['plugin'] = array(
        '#type' => 'hidden',
        '#value' => key($agent_type_options),
      );
    }
    else {
      $form['plugin'] = array(
        '#type' => 'select',
        '#title' => t('Agent Type'),
        '#options' => $agent_type_options,
        '#default_value' => '',
        '#description' => t('Choose which type of agent to create.'),
        '#weight' => -7,
      );
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validate(array $form, array &$form_state) {
    parent::validate($form, $form_state);
    // Call the plugin validate handler.
    //$this->entity->plugin->validateConfigurationForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submit(array $form, array &$form_state) {
    parent::submit($form, $form_state);

    // Add the submitted form values to the text format, and save it.
    $agent = $this->entity;
    foreach ($form_state['values'] as $key => $value) {
      if ($key != 'options') {
        $agent->set($key, $value);
      }
    }
    $agent->save();
    $form_state['redirect_route'] = $this->entity->urlInfo('edit-form');
    return $agent;
  }

}
