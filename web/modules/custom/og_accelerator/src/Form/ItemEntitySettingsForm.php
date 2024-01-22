<?php

namespace Drupal\og_accelerator\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class ItemEntitySettingsForm.
 */
class ItemEntitySettingsForm extends FormBase
{
  /**
   * Returns a unique string identifying the form.
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId()
  {
    return 'itementity_settings';
  }

  /**
   * Form submission handler.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {
  }

  /**
   * Defines the settings form for Item entities.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   * @return array
   *   Form definition array.
   */
  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $form['itementity_settings']['#markup'] = $this->t('Settings form for Item entities. Manage field settings here.');
    return $form;
  }
}
