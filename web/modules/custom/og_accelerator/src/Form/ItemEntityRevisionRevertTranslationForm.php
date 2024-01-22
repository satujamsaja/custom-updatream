<?php

namespace Drupal\og_accelerator\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\og_accelerator\Entity\ItemEntityInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for reverting an Item revision for a single translation.
 *
 * @internal
 */
class ItemEntityRevisionRevertTranslationForm extends ItemEntityRevisionRevertForm
{
  /**
   * The language to be reverted.
   *
   * @var string
   */
  protected $langcode;

  /**
   * The language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container)
  {
    $instance = parent::create($container);
    $instance->languageManager = $container->get('language_manager');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId()
  {
    return 'item_entity_revision_revert_translation_confirm';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion()
  {
    return $this->t('Are you sure you want to revert @language translation to the revision from %revision-date?', [
      '@language' => $this->languageManager->getLanguageName($this->langcode),
      '%revision-date' => $this->dateFormatter->format($this->revision->getRevisionCreationTime()),
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $item_entity_revision = NULL, $langcode = NULL)
  {
    $this->langcode = $langcode;
    $form = parent::buildForm($form, $form_state, $item_entity_revision);
    $form['revert_untranslated_fields'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Revert content shared among translations'),
      '#default_value' => FALSE,
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  protected function prepareRevertedRevision(ItemEntityInterface $revision, FormStateInterface $form_state)
  {
    $revert_untranslated_fields = $form_state->getValue('revert_untranslated_fields');
    /** @var \Drupal\og_accelerator\Entity\ItemEntityInterface $latest_revision */
    $latest_revision = $this->ItemEntityStorage->load($revision->id());
    $latest_revision_translation = $latest_revision->getTranslation($this->langcode);
    $revision_translation = $revision->getTranslation($this->langcode);
    foreach ($latest_revision_translation->getFieldDefinitions() as $field_name => $definition) {
      if ($definition->isTranslatable() || $revert_untranslated_fields) {
        $latest_revision_translation->set($field_name, $revision_translation->get($field_name)->getValue());
      }
    }
    $latest_revision_translation->setNewRevision();
    $latest_revision_translation->isDefaultRevision(TRUE);
    $revision->setRevisionCreationTime(\Drupal::time()->getRequestTime());
    return $latest_revision_translation;
  }
}
