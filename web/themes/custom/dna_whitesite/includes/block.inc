<?php

/**
 * @file
 * Theme and preprocess functions for blocks.
 */

/**
 * Implements template_preprocess_block().
 */
function dna_whitesite_preprocess_block__system_branding_block(&$variables)
{
  $variables['theme_logo_alt'] = \Drupal::token()->replace(theme_get_setting('theme_logo_alt'));
  $variables['theme_link']     = theme_get_setting('theme_link');
}
