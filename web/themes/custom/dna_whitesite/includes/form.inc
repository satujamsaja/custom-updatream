<?php

/**
 * @file
 * Theme and preprocess functions for forms.
 */

/**
 * Implements template_preprocess_form_element_label().
 */
function dna_whitesite_preprocess_form_element_label(&$variables)
{
  if (isset($variables['element']) && isset($variables['element']['#id'])) {
    if (str_contains($variables['element']['#id'] ?: '', 'edit-field-content-category-target-id')) {
      $variables['aria_label'] = 'Filter ' . $variables['element']['#title'] . ' articles';
    }
  }
}
