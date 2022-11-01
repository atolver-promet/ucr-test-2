<?php
use Drupal\ucr_core\UCRCustoms;

/**
 * Implements hook_install_tasks().
 */
function ucr_news_local_install_tasks() {
    $tasks = [];

    $tasks['ucr_news_profile_set_default_theme'] = [];
    $tasks['ucr_news_profile_set_file_settings'] = [];
    $tasks['ucr_news_profile_set_user_roles_info'] = [];
    $tasks['ucr_news_profile_set_text_editors'] = [];
    $tasks['ucr_news_profile_set_add_to_any_settings'] = [];
    $tasks['ucr_news_profile_set_menu_links'] = [];
    $tasks['ucr_news_profile_update_core_config'] = [];
    $tasks['ucr_news_local_update_profile_config'] = [];
    $tasks['ucr_news_profile_update_news_config'] = [];
    $tasks['ucr_news_profile_create_default_pages'] = [];

    return $tasks;
}

/**
 * Update Configuration Settings from the Profile
 */
function ucr_news_local_update_profile_config() {

    $ucr_default_config_files = [
        'bu.settings',
        'metatag.settings',
        'metatag.metatag_defaults.global',
        'metatag.metatag_defaults.front',
        'metatag.metatag_defaults.403',
        'metatag.metatag_defaults.404',
        'metatag.metatag_defaults.user',
        'metatag.metatag_defaults.taxonomy_term',
        'metatag.metatag_defaults.node',
        'webform.settings',
    ];

    $completed = UCRCustoms::updateConfigFromStorage('ucr_profile', $ucr_default_config_files, 'profile');

    // Add all content types to the workflow editorial.
    $workflow_config_dependencies = array();
    $all_node_types = Drupal::entityTypeManager()->getStorage('node_type')->loadMultiple();
    $node_types = array();
    foreach($all_node_types as $n_type) {
        $node_types[] = $n_type->id();
        $workflow_config_dependencies[] = 'node.type.'.$n_type->id();
    }

    if(count($node_types) > 0) {
        Drupal::configFactory()->getEditable('workflows.workflow.editorial')
            ->set('type_settings.entity_types.node', $node_types)
            ->save(TRUE);
    }

    // Add all custom block types to the workflow editorial.
    $all_block_types = Drupal::entityTypeManager()->getStorage('block_content_type')->loadMultiple();
    $block_types = array();
    foreach($all_block_types as $b_type) {
        $block_types[] = $b_type->id();
        $workflow_config_dependencies[] = 'block_content.type.'.$b_type->id();
    }

    if(count($block_types) > 0) {
        Drupal::configFactory()->getEditable('workflows.workflow.editorial')
            ->set('type_settings.entity_types.block_content', $block_types)
            ->save(TRUE);
    }

    if(count($workflow_config_dependencies) > 0) {
        Drupal::configFactory()->getEditable('workflows.workflow.editorial')
            ->set('dependencies.config', $workflow_config_dependencies)
            ->save(TRUE);
    }


    // Update the image entity browser.
    $current_config = Drupal::configFactory()->getEditable('entity_browser.browser.image_browser');
    $widgets = $current_config->get('widgets');
    foreach($widgets as $widget_uuid => $widget_data) {
        if($widget_data['id'] == 'view') {
            $current_config->set('widgets.'.$widget_uuid.'.settings.view_display', 'entity_browser_2')->save(TRUE);
        }
    }

    // Update the ck editor media entity browser.
    $current_config = Drupal::configFactory()->getEditable('entity_browser.browser.ckeditor_media_browser');
    $widgets = $current_config->get('widgets');
    foreach($widgets as $widget_uuid => $widget_data) {
        if($widget_data['id'] == 'view') {
            $current_config->set('widgets.'.$widget_uuid.'.settings.view_display', 'entity_browser_1')->save(TRUE);
        }
    }

    // Update the media entity browser.
    $current_config = Drupal::configFactory()->getEditable('entity_browser.browser.media_browser');
    $widgets = $current_config->get('widgets');
    foreach($widgets as $widget_uuid => $widget_data) {
        if($widget_data['id'] == 'view') {
            $current_config->set('widgets.'.$widget_uuid.'.settings.view_display', 'entity_browser_1')->save(TRUE);
        }
    }
}
