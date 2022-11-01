<?php

namespace Drupal\ucr_collections\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the Collection Item type entity.
 *
 * @ConfigEntityType(
 *   id = "collections_type",
 *   label = @Translation("Collection Group"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\ucr_collections\CollectionsEntityTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\ucr_collections\Form\CollectionsEntityTypeForm",
 *       "edit" = "Drupal\ucr_collections\Form\CollectionsEntityTypeForm",
 *       "delete" = "Drupal\ucr_collections\Form\CollectionsEntityTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\ucr_collections\CollectionsEntityTypeHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "collections_type",
 *   admin_permission = "administer site configuration",
 *   bundle_of = "collections",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   config_export = {
 *       "id",
 *     "label",
 *     "description",
 *     "weight"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/collections_type/{collections_type}",
 *     "add-form" = "/admin/structure/collections_type/add",
 *     "edit-form" = "/admin/structure/collections_type/{collections_type}/edit",
 *     "delete-form" = "/admin/structure/collections_type/{collections_type}/delete",
 *     "collection" = "/admin/structure/collections_type"
 *   }
 * )
 */
class CollectionsEntityType extends ConfigEntityBundleBase implements CollectionsEntityTypeInterface {

  /**
   * The Collection Item type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Collection Item type label.
   *
   * @var string
   */
  protected $label;

}
