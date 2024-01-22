<?php

namespace Drupal\og_accelerator\Controller;

use Drupal\layout_builder\Controller\ChooseBlockController as ChooseBlockControllerCore;
use Drupal\layout_builder\SectionStorageInterface;

/**
 * Defines a controller to render layout builder block selection.
 */
class ChooseBlockController extends ChooseBlockControllerCore
{
  /**
   * {@inheritdoc}
   */
  public function build(SectionStorageInterface $section_storage, $delta, $region)
  {
    $build = parent::build($section_storage, $delta, $region);
    /** Retrieve defined Layout Builder Restrictions plugins. */
    $lbr_manager = \Drupal::service('plugin.manager.layout_builder_restriction');
    $lbr_plugins = $lbr_manager->getSortedPlugins();
    foreach (array_keys($lbr_plugins) as $id) {
      $lbr_plugin = $lbr_manager->createInstance($id);
      $allowed_inline_blocks = $lbr_plugin->inlineBlocksAllowedinContext($section_storage, $delta, $region);
      /** If no inline blocks are allowed, remove the "Create a new block" link. */
      if (empty($allowed_inline_blocks)) {
        unset($build['add_block']);
      } else {
        $inline_block_list = parent::inlineBlockList($section_storage, $delta, $region);
        foreach ($inline_block_list['links']['#links'] as $key => $link) {
          $route_parameters = $link['url']->getRouteParameters();
          if (!in_array($route_parameters['plugin_id'], $allowed_inline_blocks)) {
            unset($inline_block_list['links']['#links'][$key]);
          } else {
            list(, $block_type) = explode(':', $route_parameters['plugin_id']);
            $block_config = \Drupal::config('block_content.type.' . $block_type);
            $inline_block_list['links']['#links'][$key]['attributes']['class'] = ['block-tooltip', 'use-ajax', 'js-layout-builder-block-link'];
            $inline_block_list['links']['#links'][$key]['attributes']['data-block-description'] = $block_config->get('description');
          }
        }
        $build['add_block'] = [
          '#type' => 'container',
          '#open' => true,
          '#attributes' => [
            'class' => ['block-categories', 'js-layout-builder-categories'],
          ],
          'inline_blocks' => [
            '#type' => 'details',
            '#open' => true,
            '#title' => $this->t('Create a new block'),
            '#attributes' => [
              'class' => ['js-layout-builder-category'],
            ],
            'links' => $inline_block_list['links'],
          ],
        ];
      }
    }
    /** Loads the library handling inline block tooltip. */
    $build['#attached']['library'][] = 'og_accelerator/tooltip';
    if (!empty($build['block_categories'])) {
      foreach ($build['block_categories'] as &$block_category) {
        if (!empty($block_category['#type']) && $block_category['#type'] == 'details') {
          $block_category['#open'] = 0;
        }
      }
    }
    return $build;
  }
}
