<?php

namespace Drupal\og_accelerator\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface defining an Item entity.
 */
interface ItemEntityInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface
{
  /**
   * Gets the Item name.
   *
   * @return string
   *   Name of the Item.
   */
  public function getName();

  /**
   * Sets the Item name.
   *
   * @param string $name
   *   The Item name.
   * @return \Drupal\og_accelerator\Entity\ItemEntityInterface
   *   The called Item entity.
   */
  public function setName($name);

  /**
   * Gets the Item creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Item.
   */
  public function getCreatedTime();

  /**
   * Sets the Item creation timestamp.
   *
   * @param int $timestamp
   *   The Item creation timestamp.
   * @return \Drupal\og_accelerator\Entity\ItemEntityInterface
   *   The called Item entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the Item revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Item revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   * @return \Drupal\og_accelerator\Entity\ItemEntityInterface
   *   The called Item entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Item revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Item revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   * @return \Drupal\og_accelerator\Entity\ItemEntityInterface
   *   The called Item entity.
   */
  public function setRevisionUserId($uid);
}
