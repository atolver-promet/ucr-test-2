<?php

namespace Drupal\ucr_global\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'UC Riverside Branding' block for the footer.
 *
 * @Block(
 *   id = "ucr_global_footer_branding",
 *   admin_label = @Translation("UC Riverside Footer Branding Block")
 * )
 */
class UcrGlobalFooterBrandingBlock extends BlockBase {

    /**
     * {@inheritdoc}
     */
    public function build() {
        /* get variables from the config form */
        $config = \Drupal::config('ucr_global.settings');
        // define the variable keys
        $show_links = $config->get('show_global_links');

        $build = [];
        $build['#theme'] = 'ucr_global_footer_branding_block';
        $build['#cache']['max-age'] = 1;
        $build['#show_ucr_links'] = $show_links;

        return $build;
    }
}
