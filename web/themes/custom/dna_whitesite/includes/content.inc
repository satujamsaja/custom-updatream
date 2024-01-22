<?php

/**
 * @file
 * Theme and preprocess functions for nodes.
 */

/**
 * Implements template_preprocess_node().
 */
function dna_whitesite_preprocess_node__article__full(&$variables)
{
  $variables['attributes']['vocab']    = "https://schema.org/";
  $variables['attributes']['typeof']   = "NewsArticle";
  $variables['attributes']['resource'] = "#article";
}
