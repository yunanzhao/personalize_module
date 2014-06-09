<?php

/**
 * @file
 * Contains \Drupal\personalize\Entity\OptionSet.
 */

namespace Drupal\personalize\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\FieldDefinition;
use Drupal\personalize\OptionSetInterface;

/**
 * Defines the Option Set entity class.
 *
 * @ContentEntityType(
 *   id = "option_set",
 *   label = @Translation("Variation Set"),
 *   bundle_label = @Translation("Variation Set type"),
 *   controllers = {
 *     "list_builder" = "Drupal\personalize\OptionSetListBuilder",
 *     "view_builder" = "Drupal\personalize\OptionSetViewBuilder",
 *     "form" = {
 *       "add" = "Drupal\personalize\Form\OptionSetForm",
 *       "edit" = "Drupal\personalize\Form\OptionSetForm",
 *       "delete" = "Drupal\personalize\Form\OptionSetDeleteForm",
 *       "default" = "Drupal\personalize\Form\OptionSetForm"
 *     },
 *     "translation" = "Drupal\option_set\OptionSetTranslationHandler"
 *   },
 *   admin_permission = "administer personalization",
 *   base_table = "option_set",
 *   revision_table = "option_set_revision",
 *   links = {
 *     "canonical" = "option_set.edit",
 *     "delete-form" = "option_set.delete",
 *     "edit-form" = "option_set.edit"
 *   },
 *   fieldable = TRUE,
 *   translatable = TRUE,
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "revision_id",
 *     "bundle" = "type",
 *     "label" = "info",
 *     "uuid" = "uuid"
 *   },
 *   bundle_entity_type = "option_set_type"
 * )
 */
class OptionSet extends ContentEntityBase implements OptionSetInterface {

  /**
   * {@inheritdoc}
   */
  public function createDuplicate() {
    $duplicate = parent::createDuplicate();
    $duplicate->revision_id->value = NULL;
    $duplicate->id->value = NULL;
    return $duplicate;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields['id'] = FieldDefinition::create('integer')
      ->setLabel(t('Option Set ID'))
      ->setDescription(t('The Option Set ID.'))
      ->setReadOnly(TRUE)
      ->setSetting('unsigned', TRUE);

    $fields['uuid'] = FieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The Option Set UUID.'))
      ->setReadOnly(TRUE);

    $fields['revision_id'] = FieldDefinition::create('integer')
      ->setLabel(t('Revision ID'))
      ->setDescription(t('The revision ID.'))
      ->setReadOnly(TRUE)
      ->setSetting('unsigned', TRUE);

    $fields['type'] = FieldDefinition::create('string')
      ->setLabel(t('Block type'))
      ->setDescription(t('The block type.'))
      ->setSetting('target_type', 'option_set_type')
      ->setSetting('max_length', EntityTypeInterface::BUNDLE_MAX_LENGTH);
    return $fields;
  }

}
