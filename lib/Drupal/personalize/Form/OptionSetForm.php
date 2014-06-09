<?php

/**
 * @file
 * Contains \Drupal\personalize\OptionSetForm.
 */

namespace Drupal\personalize;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Language\Language;
use Drupal\Core\Language\LanguageManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form controller for the Option Set edit forms.
 */
class OptionSetForm extends ContentEntityForm {

  /**
   * The option set storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $storage;

  /**
   * The language manager.
   *
   * @var \Drupal\Core\Language\LanguageManager
   */
  protected $languageManager;

  /**
   * Constructs a CustomBlockForm object.
   *
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager.
   * @param \Drupal\Core\Entity\EntityStorageInterface $custom_block_storage
   *   The Option Set storage.
   * @param \Drupal\Core\Language\LanguageManager $language_manager
   *   The language manager.
   */
  public function __construct(EntityManagerInterface $entity_manager, EntityStorageInterface $storage, LanguageManager $language_manager) {
    parent::__construct($entity_manager);
    $this->storage = $storage;
    $this->languageManager = $language_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $entity_manager = $container->get('entity.manager');
    return new static(
      $entity_manager,
      $entity_manager->getStorage('option_set'),
      $container->get('language_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, array &$form_state) {
    $block = $this->entity;
    $account = $this->currentUser();

    if ($this->operation == 'edit') {
      $form['#title'] = $this->t('Edit Option Set %label', array('%label' => $block->label()));
    }
    // Override the default CSS class name, since the user-defined Option Set
    // type name in 'TYPE-block-form' potentially clashes with third-party class
    // names.
    $form['#attributes']['class'][0] = drupal_html_class('block-' . $block->bundle() . '-form');


    // Add a log field if the "Create new revision" option is checked, or if the
    // current user has the ability to check that option.
    $form['revision_information'] = array(
      '#type' => 'details',
      '#title' => $this->t('Revision information'),
      // Open by default when "Create new revision" is checked.
      '#open' => $block->isNewRevision(),
      '#group' => 'advanced',
      '#attributes' => array(
        'class' => array('custom-block-form-revision-information'),
      ),
      '#attached' => array(
        'library' => array('custom_block/drupal.custom_block'),
      ),
      '#weight' => 20,
      '#access' => $block->isNewRevision() || $account->hasPermission('administer blocks'),
    );

    $form['revision_information']['revision'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Create new revision'),
      '#default_value' => $block->isNewRevision(),
      '#access' => $account->hasPermission('administer blocks'),
    );

    // Check the revision log checkbox when the log textarea is filled in.
    // This must not happen if "Create new revision" is enabled by default,
    // since the state would auto-disable the checkbox otherwise.
    if (!$block->isNewRevision()) {
      $form['revision_information']['revision']['#states'] = array(
        'checked' => array(
          'textarea[name="log"]' => array('empty' => FALSE),
        ),
      );
    }

    return parent::form($form, $form_state, $block);
  }

  /**
   * Overrides \Drupal\Core\Entity\EntityForm::submit().
   *
   * Updates the Option Set object by processing the submitted values.
   *
   * This function can be called by a "Next" button of a wizard to update the
   * form state's entity with the current step's values before proceeding to the
   * next step.
   */
  public function submit(array $form, array &$form_state) {
    // Build the block object from the submitted values.
    $block = parent::submit($form, $form_state);

    // Save as a new revision if requested to do so.
    if (!empty($form_state['values']['revision'])) {
      $block->setNewRevision();
    }

    return $block;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, array &$form_state) {
    $block = $this->entity;
    $insert = $block->isNew();
    $block->save();
    $watchdog_args = array('@type' => $block->bundle(), '%info' => $block->label());
    $block_type = entity_load('custom_block_type', $block->bundle());
    $t_args = array('@type' => $block_type->label(), '%info' => $block->label());

    if ($insert) {
      watchdog('content', '@type: added %info.', $watchdog_args, WATCHDOG_NOTICE);
      drupal_set_message($this->t('@type %info has been created.', $t_args));
    }
    else {
      watchdog('content', '@type: updated %info.', $watchdog_args, WATCHDOG_NOTICE);
      drupal_set_message($this->t('@type %info has been updated.', $t_args));
    }

    if ($block->id()) {
      $form_state['values']['id'] = $block->id();
      $form_state['id'] = $block->id();
      if ($insert) {
        if (!$theme = $block->getTheme()) {
          $theme = $this->config('system.theme')->get('default');
        }
        $form_state['redirect_route'] = array(
          'route_name' => 'block.admin_add',
          'route_parameters' => array(
            'plugin_id' => 'custom_block:' . $block->uuid(),
            'theme' => $theme,
          ),
        );
      }
      else {
        $form_state['redirect_route']['route_name'] = 'custom_block.list';
      }
    }
    else {
      // In the unlikely case something went wrong on save, the block will be
      // rebuilt and block form redisplayed.
      drupal_set_message($this->t('The block could not be saved.'), 'error');
      $form_state['rebuild'] = TRUE;
    }

    // Clear the page and block caches.
    Cache::invalidateTags(array('content' => TRUE));
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, array &$form_state) {
    if ($this->entity->isNew()) {
      $exists = $this->customBlockStorage->loadByProperties(array('info' => $form_state['values']['info']));
      if (!empty($exists)) {
        $this->setFormError('info', $form_state, $this->t('A block with description %name already exists.', array(
          '%name' => $form_state['values']['info'][0]['value'],
        )));
      }
    }
  }

}
