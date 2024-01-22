<?php

namespace Drupal\og_accelerator;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\og_accelerator\Entity\ItemEntity;

/**
 * Provides dynamic permissions for Items of different types.
 */
class ItemEntityPermissions
{
  use StringTranslationTrait;

  /**
   * Returns an array of node type permissions.
   *
   * @return array
   *   The ItemEntity by bundle permissions.
   *   @see \Drupal\user\PermissionHandlerInterface::getPermissions()
   */
  public function generatePermissions()
  {
    $perms = [];
    return $perms;
  }

  /**
   * Returns a list of node permissions for a given node type.
   *
   * @param \Drupal\og_accelerator\Entity\ItemEntity $type
   *   The ItemEntity type.
   * @return array
   *   An associative array of permission names and descriptions.
   */
  protected function buildPermissions(ItemEntity $type)
  {
    $type_id = $type->id();
    $type_params = ['%type_name' => $type->label()];
    return [
      "$type_id create entities" => [
        'title' => $this->t('Create new %type_name entities', $type_params),
      ],
      "$type_id edit own entities" => [
        'title' => $this->t('Edit own %type_name entities', $type_params),
      ],
      "$type_id edit any entities" => [
        'title' => $this->t('Edit any %type_name entities', $type_params),
      ],
      "$type_id delete own entities" => [
        'title' => $this->t('Delete own %type_name entities', $type_params),
      ],
      "$type_id delete any entities" => [
        'title' => $this->t('Delete any %type_name entities', $type_params),
      ],
      "$type_id view revisions" => [
        'title' => $this->t('View %type_name revisions', $type_params),
        'description' => $this->t('To view a revision, you also need permission to view the entity item.'),
      ],
      "$type_id revert revisions" => [
        'title' => $this->t('Revert %type_name revisions', $type_params),
        'description' => $this->t('To revert a revision, you also need permission to edit the entity item.'),
      ],
      "$type_id delete revisions" => [
        'title' => $this->t('Delete %type_name revisions', $type_params),
        'description' => $this->t('To delete a revision, you also need permission to delete the entity item.'),
      ],
    ];
  }
}
