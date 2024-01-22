<?php

namespace Drupal\og_accelerator;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Item entities.
 *
 * @see \Drupal\og_accelerator\Entity\ItemEntity
 */
class ItemEntityListBuilder extends EntityListBuilder
{
  /**
   * {@inheritdoc}
   */
  public function buildHeader()
  {
    $header['id'] = $this->t('Item ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity)
  {
    /** @var \Drupal\og_accelerator\Entity\ItemEntity $entity */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.item_entity.edit_form',
      ['item_entity' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }
}
