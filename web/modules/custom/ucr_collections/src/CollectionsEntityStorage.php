<?php

namespace Drupal\ucr_collections;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\ucr_collections\Entity\CollectionsEntityInterface;

/**
 * Defines the storage handler class for Collection Item entities.
 *
 * This extends the base storage class, adding required special handling for
 * Collection Item entities.
 *
 * @ingroup ucr_collections
 */
class CollectionsEntityStorage extends SqlContentEntityStorage implements CollectionsEntityStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(CollectionsEntityInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {collections_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {collections_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(CollectionsEntityInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {collections_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('collections_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
