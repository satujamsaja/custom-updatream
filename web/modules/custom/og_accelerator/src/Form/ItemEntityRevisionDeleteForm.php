<?php

namespace Drupal\og_accelerator\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for deleting an Item revision.
 *
 * @internal
 */
class ItemEntityRevisionDeleteForm extends ConfirmFormBase
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
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

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
    $instance->connection = $container->get('database');
    $instance->dateFormatter = $container->get('date.formatter');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId()
  {
    return 'item_entity_revision_delete_confirm';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion()
  {
    return $this->t('Are you sure you want to delete the revision from %revision-date?', [
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
    return $this->t('Delete');
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
    $this->itemEntityStorage->deleteRevision($this->revision->getRevisionId());
    $this->logger('content')->notice('Item: deleted %title revision %revision.', ['%title' => $this->revision->label(), '%revision' => $this->revision->getRevisionId()]);
    $this->messenger()->addMessage($this->t('Revision from %revision-date of Item %title has been deleted.', ['%revision-date' => $this->dateFormatter->format($this->revision->getRevisionCreationTime()), '%title' => $this->revision->label()]));
    $form_state->setRedirect(
      'entity.item_entity.canonical',
      ['item_entity' => $this->revision->id()]
    );
    if ($this->connection->query('SELECT COUNT(DISTINCT vid) FROM {item_entity_field_revision} WHERE id = :id', [':id' => $this->revision->id()])->fetchField() > 1) {
      $form_state->setRedirect(
        'entity.item_entity.version_history',
        ['item_entity' => $this->revision->id()]
      );
    }
  }
}
