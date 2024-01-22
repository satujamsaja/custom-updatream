<?php

namespace Drupal\og_accelerator\Controller;

use Drupal\node\Controller\NodeViewController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Drupal\config_pages\Entity\ConfigPages;

/**
 * Defines a controller to render an article listing node.
 */
class ArticleListController extends NodeViewController
{
  /**
   * Renders the content of a specific node.
   *
   * @return array
   */
  public function content($clean_url)
  {
    $array = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadByProperties(
        ['vid' => 'article_tags', 'field_clean_url' => $clean_url]
      );
    /** @var \Drupal\taxonomy\Entity\Term $category */
    $category = reset($array);
    if ($category) {
      $view_builder = \Drupal::service('entity_type.manager')->getViewBuilder('node');
      $node = $this->_getArticleListingNode();
      $node->setTitle('');
      $output = $view_builder->view($node);
      $output['#attached']['html_head'][] = [[
        '#tag' => 'meta',
        '#attributes' => [
          'name' => 'description',
          'content' => $category->field_meta_description->value ?: $node->body->summary,
        ],
      ], 'description'];
      return $output;
    } else {
      throw new NotFoundHttpException();
    }
  }

  /**
   * Gets the title.
   *
   * @return string
   */
  public function getTitle($clean_url)
  {
    $path = \Drupal::request()->getpathInfo();
    $last_path = basename($path);
    $node = $this->_getArticleListingNode();
    $block = null;
    foreach ($node->layout_builder__layout as $section) {
      foreach ($section->section->getComponents() as $block) {
        if ($block->getPluginId() === 'inline_block:article_listing') {
          $block = \Drupal::entityTypeManager()->getStorage('block_content')->loadRevision($block->toArray()['configuration']['block_revision_id']);
          break 2;
        }
      }
    }
    $categories = [];
    foreach (\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'article_tags']) as $taxonomy) {
      $categories[$taxonomy->field_clean_url->value] = $taxonomy->name->value;
    }
    if (!empty($block)) {
      if (isset($categories[$last_path]) && !empty($categories[$last_path])) {
        return str_replace('[filter-name]', $categories[$last_path], $block->field_article_title->value);
      }
    }
    return $categories[$clean_url] ?? 'Articles';
  }

  /**
   * Gets the node entity where the article listing is located.
   *
   * @return \Drupal\node\Entity\Node
   */
  protected function _getArticleListingNode()
  {
    $siteSettings = ConfigPages::config('site_settings');
    if ($siteSettings) {
      if (isset($siteSettings->field_article_landing_page)) {
        $landing_page_id = $siteSettings->field_article_landing_page->target_id;
        return \Drupal::service('entity_type.manager')->getStorage('node')->load($landing_page_id);
      }
    } else {
      throw new NotFoundHttpException();
    }
  }
}
