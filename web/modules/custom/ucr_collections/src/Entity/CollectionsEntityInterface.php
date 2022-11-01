<?php

namespace Drupal\ucr_collections\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Collection Item entities.
 *
 * @ingroup ucr_collections
 */
interface CollectionsEntityInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Collection Item name.
   *
   * @return string
   *   Name of the Collection Item.
   */
  public function getName();

  /**
   * Sets the Collection Item name.
   *
   * @param string $name
   *   The Collection Item name.
   *
   * @return \Drupal\ucr_collections\Entity\CollectionsEntityInterface
   *   The called Collection Item entity.
   */
  public function setName($name);

  /**
   * Gets the Collection Item creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Collection Item.
   */
  public function getCreatedTime();

  /**
   * Sets the Collection Item creation timestamp.
   *
   * @param int $timestamp
   *   The Collection Item creation timestamp.
   *
   * @return \Drupal\ucr_collections\Entity\CollectionsEntityInterface
   *   The called Collection Item entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the Collection Item revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Collection Item revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\ucr_collections\Entity\CollectionsEntityInterface
   *   The called Collection Item entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Collection Item revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Collection Item revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\ucr_collections\Entity\CollectionsEntityInterface
   *   The called Collection Item entity.
   */
  public function setRevisionUserId($uid);

}
