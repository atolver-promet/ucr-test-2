<?php

namespace Drupal\ucr_global\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'UC Riverside Footer Newsletter' block.
 *
 * @Block(
 *   id = "ucr_global_footer_newsletter",
 *   admin_label = @Translation("UC Riverside Footer Newsletter Block")
 * )
 */
class UcrGlobalFooterNewsletterBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $build['#theme'] = 'ucr_global_footer_newsletter_block';

    return $build;
  }
}
