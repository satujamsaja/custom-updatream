<?php

namespace Drupal\og_accelerator\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Overrides the controller for layout_builder.choose_block.
 */
class RouteSubscriber extends RouteSubscriberBase
{
  /**
   * {@inheritdoc}
   */
  public function alterRoutes(RouteCollection $collection)
  {
    if ($route = $collection->get('layout_builder.choose_block')) {
      $defaults = $route->getDefaults();
      $defaults['_controller'] = '\Drupal\og_accelerator\Controller\ChooseBlockController::build';
      $route->setDefaults($defaults);
    }
  }
}
