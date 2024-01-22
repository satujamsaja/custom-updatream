<?php

namespace Drupal\og_accelerator;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\node\NodeInterface;

/**
 * Defines the access control handler for the Item entity type.
 *
 * @see \Drupal\og_accelerator\Entity\ItemEntity
 */
class ItemEntityAccessControlHandler extends EntityAccessControlHandler
{
  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account)
  {
    /** @var \Drupal\og_accelerator\Entity\ItemEntityInterface $entity */
    $route_match = \Drupal::routeMatch();
    $node = $route_match->getParameter('node');
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          $permission = $this->checkOwn($entity, 'view unpublished', $account);
          if (!empty($permission)) {
            return AccessResult::allowed();
          }
          return AccessResult::allowedIfHasPermission($account, 'view unpublished item entities');
        }
        $permission = $this->checkOwn($entity, $operation, $account);
        if (!empty($permission)) {
          return AccessResult::allowed();
        }
        if ($node instanceof NodeInterface || \Drupal\jsonapi\Routing\Routes::isJsonApiRequest($route_match->getRouteObject()->getDefaults())) {
          return AccessResult::allowedIfHasPermission($account, 'view published item entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view all item revisions');
      case 'update':
        $permission = $this->checkOwn($entity, $operation, $account);
        if (!empty($permission)) {
          return AccessResult::allowed();
        }
        return AccessResult::allowedIfHasPermission($account, 'edit item entities');
      case 'delete':
        $permission = $this->checkOwn($entity, $operation, $account);
        if (!empty($permission)) {
          return AccessResult::allowed();
        }
        return AccessResult::allowedIfHasPermission($account, 'delete item entities');
    }
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL)
  {
    return AccessResult::allowedIfHasPermission($account, 'add item entities');
  }

  /**
   * Tests for given 'own' permission.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   * @param $operation
   * @param \Drupal\Core\Session\AccountInterface $account
   * @return string|null
   *   The permission string indicating it's allowed.
   */
  protected function checkOwn(EntityInterface $entity, $operation, AccountInterface $account)
  {
    /** @var \Drupal\og_accelerator\Entity\ItemEntityInterface $entity */
    $status = $entity->isPublished();
    $uid = $entity->getOwnerId();
    $is_own = $account->isAuthenticated() && $account->id() == $uid;
    if (!$is_own) {
      return;
    }
    $bundle = $entity->bundle();
    $ops = [
      'create' => '%bundle add own %bundle entities',
      'view unpublished' => '%bundle view own unpublished %bundle entities',
      'view' => '%bundle view own entities',
      'update' => '%bundle edit own entities',
      'delete' => '%bundle delete own entities',
    ];
    $permission = strtr($ops[$operation], ['%bundle' => $bundle]);
    if ($operation === 'view unpublished') {
      if (!$status && $account->hasPermission($permission)) {
        return $permission;
      } else {
        return NULL;
      }
    }
    if ($account->hasPermission($permission)) {
      return $permission;
    }
    return NULL;
  }
}
