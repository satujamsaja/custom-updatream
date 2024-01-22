<?php

namespace Drupal\og_accelerator\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides the views data for the Item entity type.
 */
class ItemEntityViewsData extends EntityViewsData
{
  /**
   * {@inheritdoc}
   */
  public function getViewsData()
  {
    $data = parent::getViewsData();
    /** Views data modification go here. */
    return $data;
  }
}
