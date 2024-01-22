<?php

namespace Drupal\og_accelerator\Plugin\Layout;

use Drupal\og_accelerator\Plugin\Layout\LayoutBase;

/**
 * Configurable layout (two column).
 *
 * @internal
 *   Plugin classes are internal.
 */
class LayoutBootstrapTwoColumn extends LayoutBase
{
  /**
   * Module name.
   *
   * @var string
   */
  protected $moduleName = 'og_accelerator';

  /**
   * Layout name.
   *
   * @var string
   */
  protected $layoutName = 'bootstrap_two_column';
}
