<?php

namespace Drupal\og_jsonapi_image_style\Controller;

use Drupal\node\Controller\NodeViewController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Drupal\config_pages\Entity\ConfigPages;
use Drupal\image\Entity\ImageStyle;
use Drupal\media\MediaInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Defines a controller to render an article listing node.
 */
class ImageStyleController
{
  /**
   * Renders the content of a specific node.
   *
   * @return array
   */
  public function content(MediaInterface $media, $image_style)
  {
    $image_uri = $media->field_media_image->entity->getFileUri();
    if ($image_style) {
      $style = ImageStyle::load($image_style);
      $destination_uri = $style->buildUri($image_uri);
      $style->createDerivative($image_uri, $destination_uri);
      $image_uri = $destination_uri;
    }
    
    $response = new BinaryFileResponse($image_uri);
    $response->headers->set('Cache-Control', 'public, max-age=3600');

    return $response;
  }
}
