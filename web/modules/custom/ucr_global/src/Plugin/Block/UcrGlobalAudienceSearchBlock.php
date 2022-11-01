<?php

namespace Drupal\ucr_global\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'UC Riverside Audience Search Block' block.
 *
 * @Block(
 *   id = "ucr_global_audience_search",
 *   admin_label = @Translation("UC Riverside Audience Search Block")
 * )
 */
class UcrGlobalAudienceSearchBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

      $build = [];
      $build['#theme'] = 'ucr_global_audience_search_block';
      $build['#cache']['max-age'] = 1;

      return $build;
  }

}
