<?php

namespace Drupal\ucr_collections;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Collection Item entity.
 *
 * @see \Drupal\ucr_collections\Entity\CollectionsEntity.
 */
class CollectionsEntityAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\ucr_collections\Entity\CollectionsEntityInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished collection item entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published collection item entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit collection item entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete collection item entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add collection item entities');
  }


}
