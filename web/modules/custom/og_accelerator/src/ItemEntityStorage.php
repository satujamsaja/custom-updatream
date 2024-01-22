<?php

namespace Drupal\og_accelerator;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\og_accelerator\Entity\ItemEntityInterface;

/**
 * Defines the storage handler class for Items.
 *
 * This extends the base storage class, adding required special handling for
 * node entities.
 */
class ItemEntityStorage extends SqlContentEntityStorage implements ItemEntityStorageInterface
{
  /**
   * {@inheritdoc}
   */
  public function revisionIds(ItemEntityInterface $entity)
  {
    return $this->database->query(
      'SELECT vid FROM {item_entity_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account)
  {
    return $this->database->query(
      'SELECT vid FROM {item_entity_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(ItemEntityInterface $entity)
  {
    return $this->database->query('SELECT COUNT(*) FROM {item_entity_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language)
  {
    return $this->database->update('item_entity_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }
}
