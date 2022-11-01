<?php

namespace Drupal\ucr_collections;

use Drupal\Core\Entity\ContentEntityStorageInterface;
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
interface CollectionsEntityStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Collection Item revision IDs for a specific Collection Item.
   *
   * @param \Drupal\ucr_collections\Entity\CollectionsEntityInterface $entity
   *   The Collection Item entity.
   *
   * @return int[]
   *   Collection Item revision IDs (in ascending order).
   */
  public function revisionIds(CollectionsEntityInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Collection Item author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Collection Item revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\ucr_collections\Entity\CollectionsEntityInterface $entity
   *   The Collection Item entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(CollectionsEntityInterface $entity);

  /**
   * Unsets the language for all Collection Item with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
