<?php
use Drupal\filter\Entity\FilterFormat;
use Drupal\menu_link_content\Entity\MenuLinkContent;
use Drupal\node\Entity\Node;
use Drupal\ucr_core\UCRCustoms;
use Drupal\user\Entity\Role;

/**
 * Implements hook_install_tasks().
 */
function ucr_news_profile_install_tasks() {
    $tasks = [];

    $tasks['ucr_news_profile_set_default_theme'] = [];
    $tasks['ucr_news_profile_set_file_settings'] = [];
    $tasks['ucr_news_profile_set_user_roles_info'] = [];
    $tasks['ucr_news_profile_set_text_editors'] = [];
    $tasks['ucr_news_profile_set_add_to_any_settings'] = [];
    $tasks['ucr_news_profile_set_menu_links'] = [];
    $tasks['ucr_news_profile_update_core_config'] = [];
    $tasks['ucr_news_profile_update_profile_config'] = [];
    $tasks['ucr_news_profile_set_workflow_options'] = [];
    $tasks['ucr_news_profile_update_news_config'] = [];
    $tasks['ucr_news_profile_set_layout_builder'] = [];
    $tasks['ucr_news_profile_create_default_pages'] = [];

    return $tasks;
}

/**
 * Sets the default and administration themes.
 */
function ucr_news_profile_set_default_theme() {
    Drupal::configFactory()
        ->getEditable('system.theme')
        ->set('default', 'ucr_news')
        ->set('admin', 'seven')
        ->save(TRUE);

    $uninstall_themes = ['bartik', 'ucr_design_1'];
    $theme_handler = \Drupal::service('theme_handler');
    foreach($uninstall_themes as $theme_name) {
        if($theme_handler->themeExists($theme_name)) {
            $theme_handler->uninstall([$theme_name]);
        }
    }

    // Use the admin theme for creating content.
    if (Drupal::moduleHandler()->moduleExists('node')) {
        Drupal::configFactory()
            ->getEditable('node.settings')
            ->set('use_admin_theme', TRUE)
            ->save(TRUE);
    }

    Drupal::configFactory()->getEditable('system.theme.global')
        ->set('logo', array('path' => '', 'url' => '', 'use_default' => TRUE))
        ->set('favicon', array('mimetype' => '', 'path' => '', 'url' => '', 'use_default' => TRUE))
        ->save(TRUE);

    // Setup the regional settings.
    Drupal::configFactory()->getEditable('system.date')
        ->set('country', array('default' => 'US'))
        ->set('timezone', array('default' => 'America/Los_Angeles', 'user' => array('configurable' => false, 'warn' => false, 'default' => '0')))
        ->save(TRUE);
}

/**
 * Sets the default file lifespan and remove temp files if they are orphaned.
 */
function ucr_news_profile_set_file_settings() {
    // Set the Temporary File settings.
    $file_settings = Drupal::configFactory()->getEditable('file.settings');
    $file_settings->set('make_unused_managed_files_temporary', TRUE)->save(TRUE);

    $file_settings = Drupal::configFactory()->getEditable('system.file');
    $file_settings->set('temporary_maximum_age', 2764800)->save(TRUE);

    // Update the Media / Field Document field to allow all of the extensions that we need.
    $field_document = Drupal::configFactory()->getEditable('field.field.media.document.field_document');
    $field_document->set('settings.file_extensions', 'txt pdf doc docx xls csv xlsx pps ppt pptx rtf zip')->save(TRUE);

    // Setup the imagemagick as the default library.
    $set_image_library = Drupal::configFactory()->getEditable('system.image');
    $set_image_library->set('toolkit', 'imagemagick')->save(TRUE);

    // Setup the imagemagick pathways.
    $set_magic = Drupal::configFactory()->getEditable('imagemagick.settings');
    $set_magic->set('path_to_binaries', '/usr/bin/')->save(TRUE);

    // Set the Lightning Media display options
    $set_lightning_media = Drupal::configFactory()->getEditable('lightning_media.settings');
    $set_lightning_media->set('entity_embed.choose_display', true)->save(TRUE);
}

/**
 * Sets the default user role setup.
 */
function ucr_news_profile_set_user_roles_info() {
    // Go ahead and if the 'content_author' default role still exists, remove it.
    $roles = Role::loadMultiple();
    $roles_to_remove = array('content_author');
    $to_update = array('anonymous','authenticated');

    $anonymous_permissions = array(
        'access content',
        'access site-wide contact form',
        'download media',
        'never autoplay videos',
        'view media',
    );

    $authenticated_permissions = array(
        'access content',
        'access shortcuts',
        'access site-wide contact form',
        'download media',
        'never autoplay videos',
        'view media',
    );

    foreach($roles as $role) {
        $role_id = $role->id();
        if(in_array($role_id, $roles_to_remove)) {
            $role->delete();
        }

        if (in_array($role_id, $to_update)) {
            switch($role_id) {
                case "anonymous": {
                    foreach($anonymous_permissions as $perm) {
                        $role->grantPermission($perm)->save(TRUE);
                    }
                    break;
                }
                case "authenticated": {
                    foreach($authenticated_permissions as $perm) {
                        $role->grantPermission($perm)->save(TRUE);
                    }
                    break;
                }
            }
        }
    }
}

/**
 * Set the default text editor setup
 */
function ucr_news_profile_set_text_editors() {
    // Check and if the original 'Rich Text' is there, remove it so it cannot be used anymore.
    $old_editor = FilterFormat::load('rich_text');
    if(is_object($old_editor)) {
        $old_editor->delete();
    }
}

/**
 * Set the Add to Any Settings
 */
function ucr_news_profile_set_add_to_any_settings() {
    // Set the Add to Any settings needed.
    Drupal::configFactory()->getEditable('addtoany.settings')
        ->set('buttons_size', 32)
        ->set('additional_html', '<a class="a2a_button_facebook"></a><a class="a2a_button_twitter"></a><a class="a2a_button_linkedin"></a><a class="a2a_button_google_plus"></a><a class="a2a_button_email"></a></a><a class="a2a_button_printfriendly"></a>')
        ->set('universal_button', 'none')
        ->set('universal_button_placement', 'after')
        ->save(TRUE);
}

/**
 * Set default menu links
 */
function ucr_news_profile_set_menu_links() {
    // Set intial Home Link
    MenuLinkContent::create(array('title' => 'Home', 'link' => array('uri' => 'internal:/'), 'menu_name' => 'main', 'expanded' => true))
        ->save();
}

/**
 * Update Configuration Settings from UCR Core
 */
function ucr_news_profile_update_core_config() {
    // Update original configuration for what we need.
    $ucr_core_config_files = [
        'views.view.content',
        'views.view.media',
        'views.view.media_library',
        'core.entity_view_display.media.document.media_library',
        'core.entity_view_display.media.image.media_library',
        'core.entity_view_display.media.video.media_library',
        'core.entity_view_display.media.document.default',
        'core.entity_view_display.media.document.embedded',
        'core.entity_view_display.media.image.default',
        'core.entity_view_display.media.image.embedded',
        'core.entity_view_display.media.video.default',
        'core.entity_view_display.media.video.embedded',
        'core.entity_form_display.node.page.default',
        'core.entity_form_display.media.image.default',
        'core.entity_form_display.media.image.media_browser',
    ];

    $completed = UCRCustoms::updateConfigFromStorage('ucr_core', $ucr_core_config_files);
}

/**
 * Update Configuration Settings from the Profile
 */
function ucr_news_profile_update_profile_config() {
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
        'purge.plugins',
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

/**
 * Add all of the content types and custom block types to the workflow.
 */
function ucr_news_profile_set_workflow_options() {
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
}

/**
 * Update Configuration Settings from the News Profile
 */
function ucr_news_profile_update_news_config() {
    $ucr_default_config_files = [
        'views.view.frontpage',
        'views.view.taxonomy_term',
    ];

    $completed = UCRCustoms::updateConfigFromStorage('ucr_news_profile', $ucr_default_config_files, 'profile');
}

/**
 * Set the Layout Builder on for the Basic Page
 */
function ucr_news_profile_set_layout_builder()
{
    LayoutBuilderEntityViewDisplay::load('node.page.default')
        ->enableLayoutBuilder()
        ->setOverridable()
        ->save(TRUE);

    Drupal::configFactory()->getEditable('core.entity_view_display.node.page.default')
        ->set("third_party_settings.layout_builder_restrictions.entity_view_mode_restriction.allowed_blocks.Auto Blocks", ['views_block:content_views-content_head_image_title_block', 'views_block:event_list-events_latest_block'])
        ->set("third_party_settings.layout_builder_restrictions.entity_view_mode_restriction.allowed_blocks.Chaos tools", [])
        ->set("third_party_settings.layout_builder_restrictions.entity_view_mode_restriction.allowed_blocks.Content", [])
        ->set("third_party_settings.layout_builder_restrictions.entity_view_mode_restriction.allowed_blocks.Content fields", ['field_block:node:page:body'])
        ->set("third_party_settings.layout_builder_restrictions.entity_view_mode_restriction.allowed_blocks.Custom Site Configuration", [])
        ->set("third_party_settings.layout_builder_restrictions.entity_view_mode_restriction.allowed_blocks.Entity Block", [])
        ->set("third_party_settings.layout_builder_restrictions.entity_view_mode_restriction.allowed_blocks.Help", [])
        ->set("third_party_settings.layout_builder_restrictions.entity_view_mode_restriction.allowed_blocks.Lists (Views)", ['views_block:who_s_online-who_s_online_block'])
        ->set("third_party_settings.layout_builder_restrictions.entity_view_mode_restriction.allowed_blocks.Moderation Dashboard", [])
        ->set("third_party_settings.layout_builder_restrictions.entity_view_mode_restriction.allowed_blocks.System", ['system_powered_by_block', 'system_branding_block'])
        ->set("third_party_settings.layout_builder_restrictions.entity_view_mode_restriction.allowed_blocks.User", [])
        ->set("third_party_settings.layout_builder_restrictions.entity_view_mode_restriction.allowed_blocks.User fields", [])
        ->set("third_party_settings.layout_builder_restrictions.entity_view_mode_restriction.allowed_blocks.core", ['page_title_block'])
        ->set("third_party_settings.layout_builder_restrictions.entity_view_mode_restriction.allowed_layouts", [
            'lb_one_column',
            'lb_two_column',
            'lb_three_column',
            'lb_four_column',
        ])
        ->save(TRUE);
}

/**
 * Create the default pages needed and set them in configuration
 */
function ucr_news_profile_create_default_pages() {
    // Create a standard 404 Error Page and set the path of it to '/404', with its pathauto turned off.
    $custom_404 = Node::create(['type' => 'page']);
    $body_404 = [
        'value' => '<h3 class="text-align-center"><strong>We\'re Sorry,</strong></h3>
<p class="text-align-center">The requested object or URL could not be found.</p>
<p class="text-align-center">The link you followed is either outdated, inaccurate, or has been removed.</p>
<p class="text-align-center">Please visit the <a href="/">homepage</a> to get back on track.</p>',
        'format' => 'rich_text_editor_limited'
    ];
    $custom_404->set('title', '404 - Content Not Found');
    $custom_404->set('body', $body_404);
    $custom_404->set('path', ['alias' => '/404', 'pathauto' => false]);
    $custom_404->status = 1;
    $custom_404->enforceIsNew();
    $custom_404->save();
    $custom_404->setPublished()->save();


    // Create a standard 403 Error Page and set the path of it to '/403', with its pathauto turned off.
    $custom_403 = Node::create(['type' => 'page']);
    $body_403 = [
        'value' => '<h3 class="text-align-center"><strong>Warning!</strong></h3>
<p class="text-align-center">You do not have permission to retrieve the URL or link you requested.</p>
<p class="text-align-center">If you believe this to be an error, please inform the administrator of the referring page.</p>',
        'format' => 'rich_text_editor_limited'
    ];
    $custom_403->set('title', '403 - Permission Denied');
    $custom_403->set('body', $body_403);
    $custom_403->set('path', ['alias' => '/403', 'pathauto' => false]);
    $custom_403->status = 1;
    $custom_403->enforceIsNew();
    $custom_403->save();
    $custom_403->setPublished()->save();

    // Set the newly created 403 and 404 pages.
    Drupal::configFactory()->getEditable('system.site')
        ->set('page.front', '/node')
        ->set('page.403', '/node/'.$custom_403->id())
        ->set('page.404', '/node/'.$custom_404->id())
        ->save(TRUE);
}
