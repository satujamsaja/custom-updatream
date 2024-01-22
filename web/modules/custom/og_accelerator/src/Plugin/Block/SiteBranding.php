<?php

namespace Drupal\og_accelerator\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a site branding block.
 *
 * @Block(
 *   id = "og_accelerator_site_branding",
 *   admin_label = @Translation("Site Branding"),
 *   category = @Translation("Accelerator")
 * )
 */
class SiteBranding extends BlockBase implements ContainerFactoryPluginInterface
{
  /**
   * Stores the configuration factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Creates a SystemBrandingBlock instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ConfigFactoryInterface $config_factory) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state)
  {
    $form   = parent::blockForm($form, $form_state);
    $config = $this->getConfiguration();

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $site_config = $this->configFactory->get('system.site');

    $build = [
      '#theme' => 'site_branding'
    ];

    $build['#text']   = $site_config->get('name');
    $build['#image']  = '';
    $build['#width']  = 124;
    $build['#height'] = 49;
    $build['#path']   = theme_get_setting('theme_link');
    $build['#alt']    = \Drupal::token()->replace(theme_get_setting('theme_logo_alt'));

    $siteSettings = \Drupal\config_pages\Entity\ConfigPages::config('site_settings');

    if ($siteSettings->field_site_logo->first()) {
      $build['#image'] = $siteSettings->field_site_logo->first()->entity->field_media_image->entity->createFileUrl();
      $build['#alt']   = $siteSettings->field_site_logo->first()->entity->field_media_image->alt;
    }

    if ($siteSettings->field_site_logo_link->first()) {
      $build['#path'] = $siteSettings->field_site_logo_link->first()->getUrl()->toString();
    }

    return $build;
  }

}
