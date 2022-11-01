<?php

namespace Drupal\ucr_collections\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Collection Item entities.
 */
class CollectionsEntityViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.
    return $data;
  }

}
