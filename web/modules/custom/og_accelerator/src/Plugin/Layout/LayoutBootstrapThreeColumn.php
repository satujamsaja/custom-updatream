<?php

namespace Drupal\og_accelerator\Plugin\Layout;

use Drupal\og_accelerator\Plugin\Layout\LayoutBase;

/**
 * Configurable layout (three column).
 *
 * @internal
 *   Plugin classes are internal.
 */
class LayoutBootstrapThreeColumn extends LayoutBase
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
  protected $layoutName = 'bootstrap_three_column';
}
