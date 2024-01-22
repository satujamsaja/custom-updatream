<?php

/**
 * @file
 * Theme and preprocess functions for fields.
 */

/**
 * Implements template_preprocess_field().
 */
function dna_whitesite_preprocess_field__node__field_content_category__article__full(&$variables)
{
  $variables['attributes']['class'][] = 'article-category';
  $siteSettings = \Drupal\config_pages\Entity\ConfigPages::config('site_settings');
  $variables['terms'] = [];
  foreach ($variables['items'] as $item) {
    if (isset($item['content']['#url']) && $item['content']['#url'] instanceof \Drupal\Core\Url) {
      $term = [];
      $landing_page_url = '/';
      if ($siteSettings->field_article_landing_page->entity) {
        $landing_page_url = $siteSettings->field_article_landing_page->entity->toUrl()->toString() . '/';
      }
      $term['listing_url'] =  $landing_page_url . $item['content']['#options']['entity']->field_clean_url->value;
      $term['term_name'] = $item['content']['#options']['entity']->name->value;
      $variables['terms'][] = $term;
    }
  }
}
function dna_whitesite_preprocess_field__node__title__article__full(&$variables)
{
  $variables['attributes']['class'][] = 'article-title';
}
function dna_whitesite_preprocess_field__node__body__article__full(&$variables)
{
  $variables['attributes']['class'][] = 'article-body';
}
function dna_whitesite_preprocess_field__node__field_article_date__article__full(&$variables)
{
  $variables['attributes']['class'][] = 'article-date';
}
function dna_whitesite_preprocess_field__node__field_hero_image__article__full(&$variables)
{
  $variables['attributes']['class'][] = 'article-hero-image';
}
