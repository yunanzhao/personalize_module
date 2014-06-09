<?php

/**
 * @file
 * Contains \Drupal\pesronalize\PersonalizeAgentListController.
 */

namespace Drupal\personalize;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityControllerInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Routing\UrlGeneratorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a listing of image styles.
 */
class AgentListBuilder extends ConfigEntityListBuilder implements EntityControllerInterface {

  /**
   * The URL generator.
   *
   * @var \Drupal\Core\Routing\UrlGeneratorInterface
   */
  protected $urlGenerator;

  /**
   * Constructs a new ImageStyleListController object.
   *
   * @param string $entity_type
   *   The type of entity to be listed.
   * @param array $entity_info
   *   An array of entity info for the entity type.
   * @param \Drupal\Core\Entity\EntityStorageInterface $image_style_storage
   *   The image style entity storage controller class.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke hooks on.
   * @param \Drupal\Core\Routing\UrlGeneratorInterface $url_generator
   *   The URL generator.
   */
  public function __construct($entity_type, EntityStorageInterface $storage, ModuleHandlerInterface $module_handler, UrlGeneratorInterface $url_generator) {
    parent::__construct($entity_type, $storage, $module_handler);
    $this->urlGenerator = $url_generator;
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type,
      $container->get('entity.manager')->getStorage($entity_type->id()),
      $container->get('module_handler'),
      $container->get('url_generator'),
      $container->get('string_translation')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Title');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['label'] = $this->getLabel($entity);
    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function render() {
    $build = parent::render();
    $build['#empty'] = $this->t('There are currently no campaigns. <a href="!url">Add a new one</a>.', array(
      '!url' => $this->urlGenerator->generateFromRoute('personalize.agent_add'),
    ));
    return $build;
  }

}
