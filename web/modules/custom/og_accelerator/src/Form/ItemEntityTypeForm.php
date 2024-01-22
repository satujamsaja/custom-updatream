<?php

namespace Drupal\og_accelerator\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a form for adding/editing an Item Type bundle.
 *
 * @internal
 */
class ItemEntityTypeForm extends EntityForm
{
  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state)
  {
    $form = parent::form($form, $form_state);
    $item_entity_type = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $item_entity_type->label(),
      '#description' => $this->t("Label for the Item type."),
      '#required' => TRUE,
    ];
    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $item_entity_type->id(),
      '#machine_name' => [
        'exists' => '\Drupal\og_accelerator\Entity\ItemEntityType::load',
      ],
      '#disabled' => !$item_entity_type->isNew(),
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state)
  {
    $item_entity_type = $this->entity;
    $status = $item_entity_type->save();
    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Item type.', [
          '%label' => $item_entity_type->label(),
        ]));
        break;
      default:
        $this->messenger()->addMessage($this->t('Saved the %label Item type.', [
          '%label' => $item_entity_type->label(),
        ]));
    }
    $form_state->setRedirectUrl($item_entity_type->toUrl('collection'));
  }
}
