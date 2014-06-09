<?php

/**
 * @file
 * Contains \Drupal\personalize\OptionSetListBuilder.
 */

namespace Drupal\personalize;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;

/**
 * Defines a class to build a listing of custom block entities.
 */
class OptionSetListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = t('Variation Set name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['label'] = $this->getLabel($entity);
    return $row + parent::buildRow($entity);
  }

}
