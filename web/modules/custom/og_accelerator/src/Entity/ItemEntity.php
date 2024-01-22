<?php

namespace Drupal\og_accelerator\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EditorialContentEntityBase;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\user\UserInterface;

/**
 * Defines the Item entity class.
 *
 * @ContentEntityType(
 *   id = "item_entity",
 *   label = @Translation("Item"),
 *   bundle_label = @Translation("Item type"),
 *   handlers = {
 *     "storage" = "Drupal\og_accelerator\ItemEntityStorage",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\og_accelerator\ItemEntityListBuilder",
 *     "views_data" = "Drupal\og_accelerator\Entity\ItemEntityViewsData",
 *     "translation" = "Drupal\og_accelerator\ItemEntityTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\og_accelerator\Form\ItemEntityForm",
 *       "add" = "Drupal\og_accelerator\Form\ItemEntityForm",
 *       "edit" = "Drupal\og_accelerator\Form\ItemEntityForm",
 *       "delete" = "Drupal\og_accelerator\Form\ItemEntityDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\og_accelerator\ItemEntityHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\og_accelerator\ItemEntityAccessControlHandler",
 *   },
 *   base_table = "item_entity",
 *   data_table = "item_entity_field_data",
 *   revision_table = "item_entity_revision",
 *   revision_data_table = "item_entity_field_revision",
 *   revision_metadata_keys = {
 *     "revision_user" = "revision_user",
 *     "revision_created" = "revision_created",
 *     "revision_log_message" = "revision_log_message",
 *   },
 *   translatable = TRUE,
 *   permission_granularity = "bundle",
 *   admin_permission = "administer item entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "vid",
 *     "bundle" = "type",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/item_entity/{item_entity}",
 *     "add-page" = "/admin/structure/item_entity/add",
 *     "add-form" = "/admin/structure/item_entity/add/{item_entity_type}",
 *     "edit-form" = "/admin/structure/item_entity/{item_entity}/edit",
 *     "delete-form" = "/admin/structure/item_entity/{item_entity}/delete",
 *     "version-history" = "/admin/structure/item_entity/{item_entity}/revisions",
 *     "revision" = "/admin/structure/item_entity/{item_entity}/revisions/{item_entity_revision}/view",
 *     "revision_revert" = "/admin/structure/item_entity/{item_entity}/revisions/{item_entity_revision}/revert",
 *     "revision_delete" = "/admin/structure/item_entity/{item_entity}/revisions/{item_entity_revision}/delete",
 *     "translation_revert" = "/admin/structure/item_entity/{item_entity}/revisions/{item_entity_revision}/revert/{langcode}",
 *     "collection" = "/admin/structure/item_entity",
 *   },
 *   bundle_entity_type = "item_entity_type",
 *   field_ui_base_route = "entity.item_entity_type.edit_form"
 * )
 */
class ItemEntity extends EditorialContentEntityBase implements ItemEntityInterface
{
  use EntityChangedTrait;
  use EntityPublishedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values)
  {
    parent::preCreate($storage_controller, $values);
    $values += [
      'user_id' => \Drupal::currentUser()->id(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function urlRouteParameters($rel)
  {
    $uri_route_parameters = parent::urlRouteParameters($rel);
    if ($rel === 'revision_revert' && $this instanceof RevisionableInterface) {
      $uri_route_parameters[$this->getEntityTypeId() . '_revision'] = $this->getRevisionId();
    } else if ($rel === 'revision_delete' && $this instanceof RevisionableInterface) {
      $uri_route_parameters[$this->getEntityTypeId() . '_revision'] = $this->getRevisionId();
    }
    return $uri_route_parameters;
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage)
  {
    parent::preSave($storage);
    foreach (array_keys($this->getTranslationLanguages()) as $langcode) {
      /** @var \Drupal\og_accelerator\Entity\ItemEntity $translation */
      $translation = $this->getTranslation($langcode);
      /** If no owner has been set explicitly, make the anonymous user the owner. */
      if (!$translation->getOwner()) {
        $translation->setOwnerId(0);
      }
    }
    /** If no revision author has been set explicitly, make the item_entity owner the revision author. */
    if (!$this->getRevisionUser()) {
      $this->setRevisionUserId($this->getOwnerId());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getName()
  {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name)
  {
    $this->set('name', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime()
  {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp)
  {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner()
  {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId()
  {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid)
  {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account)
  {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type)
  {
    $fields = parent::baseFieldDefinitions($entity_type);
    $fields += static::publishedBaseFieldDefinitions($entity_type);
    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(new TranslatableMarkup('Authored by'))
      ->setDescription(new TranslatableMarkup('The user ID of author of the Item entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);
    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Name'))
      ->setDescription(new TranslatableMarkup('The name of the Item entity.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 255,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);
    /** @var \Drupal\Core\Field\BaseFieldDefinition $field_status */
    $field_status = $fields['status'];
    $field_status->setDescription(new TranslatableMarkup('A boolean indicating whether the Item is published.'))
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'region' => 'hidden',
        'weight' => -3,
      ]);
    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(new TranslatableMarkup('Created'))
      ->setDescription(new TranslatableMarkup('The time that the entity was created.'));
    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(new TranslatableMarkup('Changed'))
      ->setDescription(new TranslatableMarkup('The time that the entity was last edited.'))
      ->setRevisionable(TRUE);
    $fields['revision_translation_affected'] = BaseFieldDefinition::create('boolean')
      ->setLabel(new TranslatableMarkup('Revision translation affected'))
      ->setDescription(new TranslatableMarkup('Indicates if the last edit of a translation belongs to current revision.'))
      ->setReadOnly(TRUE)
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE);
    return $fields;
  }
}
