<?php

namespace Drupal\og_accelerator\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a button block.
 *
 * @Block(
 *   id = "og_accelerator_button_block",
 *   admin_label = @Translation("Button Block"),
 *   category = @Translation("Accelerator")
 * )
 */
class ButtonBlock extends BlockBase
{

  /**
   * {@inheritdoc}
   */
  public function build()
  {
    $config = $this->getConfiguration();
    $build = [
      '#theme' => 'button_block',
      '#data'  => $config,
    ];
    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state)
  {
    $form   = parent::blockForm($form, $form_state);
    $config = $this->getConfiguration();
    $form['button_label'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Button label'),
      '#default_value' => $config['button_label'] ?? '',
      '#required'      => true,
    ];
    $form['href_attribute'] = [
      '#type' => 'linkit',
      '#title' => $this->t('Link'),
      '#description' => $this->t('Start typing to see a list of results. Click to select.'),
      '#autocomplete_route_name' => 'linkit.autocomplete',
      '#autocomplete_route_parameters' => [
        'linkit_profile_id' => 'default',
      ],
      '#default_value' => isset($config['href_attribute']) ? $config['href_attribute'] : '',
      '#required'      => true,
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state)
  {
    parent::blockSubmit($form, $form_state);
    $this->configuration['button_label']   = $form_state->getValue('button_label');
    $this->configuration['href_attribute'] = $form_state->getValue('href_attribute');
  }
}
