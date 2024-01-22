<?php

/**
 * @file
 * Contains item_entity.page.inc.
 *
 * Page callback for Item entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Item templates.
 *
 * Default template: item_entity.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_item_entity(array &$variables)
{
  /** Fetch ItemEntity Entity Object. */
  $item_entity = $variables['elements']['#item_entity'];
  /** Helpful $content variable for templates. */
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
