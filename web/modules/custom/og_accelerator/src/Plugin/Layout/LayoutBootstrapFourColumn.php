<?php

namespace Drupal\og_accelerator\Plugin\Layout;

use Drupal\og_accelerator\Plugin\Layout\LayoutBase;

/**
 * Configurable layout (four column).
 *
 * @internal
 *   Plugin classes are internal.
 */
class LayoutBootstrapFourColumn extends LayoutBase
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
  protected $layoutName = 'bootstrap_four_column';
}
