<?php

use Drupal\node\NodeInterface;

/**
 * @file
 * Custom theme hooks.
 */

/**
 * Implements hook_library_info_build().
 */
function dna_whitesite_library_info_build()
{
  $extensions = ['css', 'js'];
  $directory = 'themes/custom/dna_whitesite/assets/components';
  $extensions = array_map('preg_quote', $extensions);
  $extensions = implode('|', $extensions);
  if (!is_dir($directory)) {
    return [];
  }
  $file_scan = \Drupal::service('file_system')->scanDirectory($directory, "/{$extensions}$/");
  $libraries = [];
  foreach ($file_scan as $file) {
    $parts = explode('.', $file->filename);
    $extension = end($parts);
    switch ($extension) {
      case 'css':
        $libraries[$file->name][$extension] = [
          'component' => [
            '/' . $file->uri => [],
          ],
        ];
        break;
      case 'js':
        $libraries[$file->name][$extension] = [
          '/' . $file->uri => [],
        ];
        break;
    }
  }
  return $libraries;
}

/**
 * Implements template_preprocess_html().
 */
function dna_whitesite_preprocess_html(&$variables)
{
  $siteSettings = \Drupal\config_pages\Entity\ConfigPages::config('site_settings');
  if ($siteSettings) {
    if ($siteSettings->field_gtm_snippet) {
      if ($siteSettings->field_enable_headless && $siteSettings->field_enable_headless->value == "0") {
        $variables['gtm_snippet'] = $siteSettings->field_gtm_snippet->value;
      }
    }
    if ($siteSettings->field_gtm_snippet_2) {
      if ($siteSettings->field_enable_headless && $siteSettings->field_enable_headless->value == "0") {
        $variables['gtm_snippet_2'] = $siteSettings->field_gtm_snippet_2->value;
      }
    }
  }
  $entity = \Drupal::entityTypeManager()
    ->getStorage('item_entity');
  $query = $entity->getQuery();
  $ids = $query->condition('type', 'background_colour')
    ->sort('field_weight', 'asc')
    ->accessCheck(TRUE)
    ->execute();
  $backgrounds = $entity->loadMultiple($ids);
  $styleTag = '<style> :root { ';
  $ovbgcolourTag = '<style>';
  foreach ($backgrounds as $background) {
    if (!empty($background->field_color->color)) {
      $slug = strtolower(preg_replace('/[^a-zA-Z0-9-]+/', '-', $background->name->value));
      $styleTag .= ' --bgov--' . $slug . ': ' . $background->field_color->color . ' !important; ';
      $ovbgcolourTag .= '.' . $background->field_class->value . ' { --section-bgcolor: ' . $background->field_color->color . ' !important; background-color: var(--bgov--' . $slug . ') !important; } ';
    }
  }
  $styleTag .= ' } </style>';
  $ovbgcolourTag .= '</style>';
  $variables['ovbg_snippet'] = $styleTag;
  $variables['ovbg_colour_snippet'] = $ovbgcolourTag;
}

/**
 * Implements template_page_attachments_alter().
 */
function dna_whitesite_page_attachments_alter(array &$attachments)
{
  // Hide page from search engine if exclude sitemap checked.
  $sitemap_exclude = FALSE;
  $node = \Drupal::routeMatch()->getParameter('node');
  if ($node instanceof NodeInterface) {
    if ($node->hasField('field_exclude_sitemap')) {
      $sitemap_settings = $node->get('field_exclude_sitemap')->value;
      if (isset($sitemap_settings) && $sitemap_settings == "1") {
        $sitemap_exclude = TRUE;
      }
    }
  }

  $page_excluded = [
    '/search'
  ];

  $current_path = \Drupal::service('path.current')->getPath();
  if (in_array($current_path, $page_excluded)) {
    $robots_exist = FAlSE;
    if (isset($attachments['#attached']['html_head']) && !empty($attachments['#attached']['html_head'])) {
      foreach ($attachments['#attached']['html_head'] as $attachment_key => $attachment) {
        $attachment_values = array_values($attachment);
        if (isset($attachment_values[1]) && $attachment_values[1] == 'robots') {
          $robots_exist = TRUE;
          $attachments['#attached']['html_head'][$attachment_key][0]['#attributes']['content'] = 'noindex, nofollow';
        }
      }
    }

    if ($robots_exist == FALSE) {
      $attachments['#attached']['html_head'][] = [
        [
          '#tag' => 'meta',
          '#attributes' => [
            'name' => 'robots',
            'content' => 'noindex, nofollow',
          ],
        ],
        'robots',
      ];
    }
  }
  $current_path = \Drupal::service('path.current')->getPath();
  if (isset($current_path) && preg_match('/webform/', $current_path)) {
    $attachments['#attached']['html_head'][] = [
      [
        '#tag' => 'meta',
        '#attributes' => [
          'name' => 'robots',
          'content' => 'noindex, nofollow',
        ],
      ],
      'robots',
    ];
  }
}

/**
 * Implements hook_form_system_theme_settings_alter().
 */
function dna_whitesite_form_system_theme_settings_alter(&$form, $form_state)
{
  if (isset($form_id)) {
    return;
  }
  $form['logo']['settings']['theme_logo_alt'] = [
    '#type'          => 'textfield',
    '#title'         => t('Logo alt'),
    '#default_value' => theme_get_setting('theme_logo_alt'),
    '#description'   => t('Alt text of the logo image. Token is supported, example: [site:name]'),
    '#weight'        => 1,
    '#required'      => true,
  ];
  $form['logo']['settings']['theme_link'] = [
    '#type'          => 'textfield',
    '#title'         => t('Logo link'),
    '#default_value' => theme_get_setting('theme_link'),
    '#description'   => t('Url when the logo is clicked.'),
    '#weight'        => 2,
    '#required'      => true,
  ];
}

/**
 * Implements template_preprocess_rdf_metadata().
 */
function dna_whitesite_preprocess_rdf_metadata(&$variables)
{
  foreach ($variables['metadata'] as $key => $attributes) {
    if (!is_null($attributes)) {
      $variables['metadata'][$key] = new Attribute($attributes);
    } else {
      $variables['metadata'][$key] = new Attribute();
    }
  }
}
