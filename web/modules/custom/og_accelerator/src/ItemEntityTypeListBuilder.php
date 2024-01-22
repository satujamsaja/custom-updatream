<?php

namespace Drupal\og_accelerator;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * Defines a class to build a listing of Item Type bundles.
 *
 * @see \Drupal\og_accelerator\Entity\ItemEntityType
 */
class ItemEntityTypeListBuilder extends ConfigEntityListBuilder
{
  /**
   * {@inheritdoc}
   */
  public function buildHeader()
  {
    $header['label'] = $this->t('Item type');
    $header['id'] = $this->t('Machine name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity)
  {
    $row['label'] = $entity->label();
    $row['id'] = $entity->id();
    return $row + parent::buildRow($entity);
  }
}
