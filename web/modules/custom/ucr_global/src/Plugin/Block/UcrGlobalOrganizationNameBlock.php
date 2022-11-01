<?php

namespace Drupal\ucr_global\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Organization name' block.
 *
 * @Block(
 *   id = "ucr_global_organization_name",
 *   admin_label = @Translation("UC Riverside Organization Name Block")
 * )
 */
class UcrGlobalOrganizationNameBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = \Drupal::config('ucr_global.settings');
    $organization_name = $config->get('organization_name');
    $organization_url = $config->get('organization_url');

    $build = [];
    $build['#theme'] = 'ucr_global_organization_name_block';
    $build['#cache']['max-age'] = 1;
    $build['#org_name'] = $organization_name;
    $build['#org_url'] = $organization_url;

    return $build;
  }

}
