<?php

namespace Drupal\og_accelerator\Plugin\Layout;

use Drupal\og_accelerator\Plugin\Layout\LayoutBase;

/**
 * Configurable layout (one column).
 *
 * @internal
 *   Plugin classes are internal.
 */
class LayoutBootstrapOneColumn extends LayoutBase
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
  protected $layoutName = 'bootstrap_one_column';
}
