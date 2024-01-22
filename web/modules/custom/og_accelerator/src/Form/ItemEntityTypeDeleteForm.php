<?php

namespace Drupal\og_accelerator\Form;

use Drupal\Core\Entity\EntityConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Provides a form for Item Type deletion.
 *
 * @internal
 */
class ItemEntityTypeDeleteForm extends EntityConfirmFormBase
{
  /**
   * {@inheritdoc}
   */
  public function getQuestion()
  {
    return $this->t('Are you sure you want to delete %name?', ['%name' => $this->entity->label()]);
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl()
  {
    return new Url('entity.item_entity_type.collection');
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText()
  {
    return $this->t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    $this->entity->delete();
    $this->messenger()->addMessage(
      $this->t('@type: @label deleted.', [
        '@type' => $this->entity->bundle(),
        '@label' => $this->entity->label(),
      ])
    );
    $form_state->setRedirectUrl($this->getCancelUrl());
  }
}
