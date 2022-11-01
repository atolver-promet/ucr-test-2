<?php

namespace Drupal\ucr_global\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'UC Riverside Search' block designed for the footer area.
 *
 * @Block(
 *   id = "ucr_global_footer_search",
 *   admin_label = @Translation("UC Riverside Footer Search Block")
 * )
 */
class UcrGlobalFooterSearchBlock extends BlockBase {
    /**
     * {@inheritdoc}
     */
    public function build() {

        $build = [];
        $build['#theme'] = 'ucr_global_footer_search_block';
        $build['#cache']['max-age'] = 1;
        return $build;
    }
}
