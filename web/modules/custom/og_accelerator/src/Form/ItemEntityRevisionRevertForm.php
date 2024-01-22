<?php

namespace Drupal\og_accelerator\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\og_accelerator\Entity\ItemEntityInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for reverting an Item revision.
 *
 * @internal
 */
class ItemEntityRevisionRevertForm extends ConfirmFormBase
{
  /**
   * The Item revision.
   *
   * @var \Drupal\og_accelerator\Entity\ItemEntityInterface
   */
  protected $revision;

  /**
   * The Item storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $itemEntityStorage;

  /**
   * The date formatter service.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container)
  {
    $instance = parent::create($container);
    $instance->itemEntityStorage = $container->get('entity_type.manager')->getStorage('item_entity');
    $instance->dateFormatter = $container->get('date.formatter');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId()
  {
    return 'item_entity_revision_revert_confirm';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion()
  {
    return $this->t('Are you sure you want to revert to the revision from %revision-date?', [
      '%revision-date' => $this->dateFormatter->format($this->revision->getRevisionCreationTime()),
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl()
  {
    return new Url('entity.item_entity.version_history', ['item_entity' => $this->revision->id()]);
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText()
  {
    return $this->t('Revert');
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription()
  {
    return '';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $item_entity_revision = NULL)
  {
    $this->revision = $this->itemEntityStorage->loadRevision($item_entity_revision);
    $form = parent::buildForm($form, $form_state);
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    /**
     * The revision timestamp will be updated when the revision is saved.
     * Keep the original one for the confirmation message.
     */
    $original_revision_timestamp = $this->revision->getRevisionCreationTime();
    $this->revision = $this->prepareRevertedRevision($this->revision, $form_state);
    $this->revision->changed = \Drupal::time()->getRequestTime();
    $this->revision->revision_log = $this->t('Copy of the revision from %date.', [
      '%date' => $this->dateFormatter->format($original_revision_timestamp),
    ]);
    $this->revision->save();
    $this->logger('content')->notice('Item: reverted %title revision %revision.', ['%title' => $this->revision->label(), '%revision' => $this->revision->getRevisionId()]);
    $this->messenger()->addMessage($this->t('Item %title has been reverted to the revision from %revision-date.', ['%title' => $this->revision->label(), '%revision-date' => $this->dateFormatter->format($original_revision_timestamp)]));
    $form_state->setRedirect(
      'entity.item_entity.version_history',
      ['item_entity' => $this->revision->id()]
    );
  }

  /**
   * Prepares a revision to be reverted.
   *
   * @param \Drupal\og_accelerator\Entity\ItemEntityInterface $revision
   *   The revision to be reverted.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   * @return \Drupal\og_accelerator\Entity\ItemEntityInterface
   *   The prepared revision ready to be stored.
   */
  protected function prepareRevertedRevision(ItemEntityInterface $revision, FormStateInterface $form_state)
  {
    $revision->setNewRevision();
    $revision->isDefaultRevision(TRUE);
    $revision->setRevisionCreationTime(\Drupal::time()->getRequestTime());
    return $revision;
  }
}
