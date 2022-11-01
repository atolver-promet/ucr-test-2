<?php
use Drupal\ucr_core\UCRCustoms;

/**
 * Implements hook_install_tasks().
 */
function student_services_local_install_tasks() {
    $tasks = [];

    $tasks['student_services_set_default_theme'] = [];
    $tasks['student_services_set_file_settings'] = [];
    $tasks['student_services_set_user_roles_info'] = [];
    $tasks['student_services_set_text_editors'] = [];
    $tasks['student_services_set_add_to_any_settings'] = [];
    $tasks['student_services_set_menu_links'] = [];
    $tasks['student_services_update_core_config'] = [];
    $tasks['student_services_local_update_profile_config'] = [];
    $tasks['student_services_set_workflow_options'] = [];
    $tasks['student_services_set_layout_builder'] = [];
    $tasks['student_services_create_default_pages'] = [];

    return $tasks;
}

/**
 * Update Configuration Settings from the Profile
 */
function student_services_local_update_profile_config() {

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
