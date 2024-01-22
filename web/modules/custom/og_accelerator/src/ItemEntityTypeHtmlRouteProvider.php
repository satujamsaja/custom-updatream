<?php

namespace Drupal\og_accelerator;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\Routing\AdminHtmlRouteProvider;

/**
 * Provides routing information for Item Type bundles.
 */
class ItemEntityTypeHtmlRouteProvider extends AdminHtmlRouteProvider
{
  /**
   * {@inheritdoc}
   */
  public function getRoutes(EntityTypeInterface $entity_type)
  {
    $collection = parent::getRoutes($entity_type);
    return $collection;
  }
}
