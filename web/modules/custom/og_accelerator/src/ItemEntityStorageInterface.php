<?php

namespace Drupal\og_accelerator;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\og_accelerator\Entity\ItemEntityInterface;

/**
 * Defines an interface for Item entity storage classes.
 */
interface ItemEntityStorageInterface extends ContentEntityStorageInterface
{
  /**
   * Gets a list of Item revision IDs for a specific Item.
   *
   * @param \Drupal\og_accelerator\Entity\ItemEntityInterface $entity
   *   The Item entity.
   * @return int[]
   *   Item revision IDs (in ascending order).
   */
  public function revisionIds(ItemEntityInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Item author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   * @return int[]
   *   Item revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\og_accelerator\Entity\ItemEntityInterface $entity
   *   The Item entity.
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(ItemEntityInterface $entity);

  /**
   * Unsets the language for all Item with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);
}
