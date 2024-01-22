<?php

namespace Drupal\og_accelerator;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Extends Twig_Extension class.
 */
class HelperTwigExtensions extends AbstractExtension
{
  /**
   * {@inheritdoc}
   */
  public function getName()
  {
    return 'og_accelerator.twig_extensions';
  }

  /**
   * {@inheritdoc}
   */
  public function getFilters()
  {
    return [
      new TwigFilter('slug', [$this, 'createSlug']),
      new TwigFilter('embed_video_url', [$this, 'getEmbedVideoUrl']),
    ];
  }

  /**
   * Creates a slug from a string input.
   */
  public function createSlug($title, $separator = '-')
  {
    $flip = $separator === '-' ? '_' : '-';
    $title = preg_replace('![' . preg_quote($flip) . ']+!u', $separator, $title);
    $title = str_replace('@', $separator . 'at' . $separator, $title);
    $title = preg_replace('![^' . preg_quote($separator) . '\pL\pN\s]+!u', '', mb_strtolower($title, 'UTF-8'));
    $title = preg_replace('![' . preg_quote($separator) . '\s]+!u', $separator, $title);
    return trim($title, $separator);
  }

  /**
   * Gets embed url of a youtube or vimeo video.
   */
  public function getEmbedVideoUrl($video_url)
  {
    return og_accelerator_get_embed_video_url($video_url);
  }
}
