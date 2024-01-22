<?php

namespace Drupal\og_accelerator\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the Item Type class.
 *
 * @ConfigEntityType(
 *   id = "item_entity_type",
 *   label = @Translation("Item type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\og_accelerator\ItemEntityTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\og_accelerator\Form\ItemEntityTypeForm",
 *       "edit" = "Drupal\og_accelerator\Form\ItemEntityTypeForm",
 *       "delete" = "Drupal\og_accelerator\Form\ItemEntityTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\og_accelerator\ItemEntityTypeHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "item_entity_type",
 *   config_export = {
 *     "id",
 *     "label",
 *   },
 *   admin_permission = "administer site configuration",
 *   bundle_of = "item_entity",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/item_entity_type/{item_entity_type}",
 *     "add-form" = "/admin/structure/item_entity_type/add",
 *     "edit-form" = "/admin/structure/item_entity_type/{item_entity_type}/edit",
 *     "delete-form" = "/admin/structure/item_entity_type/{item_entity_type}/delete",
 *     "collection" = "/admin/structure/item_entity_type"
 *   }
 * )
 */
class ItemEntityType extends ConfigEntityBundleBase implements ItemEntityTypeInterface
{
  /**
   * The Item Type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Item Type label.
   *
   * @var string
   */
  protected $label;
}
