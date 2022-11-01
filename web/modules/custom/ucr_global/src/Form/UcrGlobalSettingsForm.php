<?php

namespace Drupal\ucr_global\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Config\ConfigFactoryInterface;

use Drupal\node\Entity\Node;

/**
 * Implements a form to collect security check configuration.
 */
class UcrGlobalSettingsForm extends ConfigFormBase {
	public function getFormId() {
		return 'ucr_global_settings_form';
	}

	/**
	 * {@inheritdoc}
	 */
	protected function getEditableConfigNames() {
		return [ 'ucr_global.settings' ];
	}

	/**
	 * {@inheritdoc}.
	 */
	public function buildForm( array $form, FormStateInterface $form_state ) {
		$module_path = drupal_get_path( 'module', 'ucr_global' );
		$ucr_config = \Drupal::config('ucr_global.settings');

		$search_results_page_entity = "";
		$srp_config = $ucr_config->get('search_results_page');
		if(!empty($srp_config)) {
            $search_results_page_entity = Node::load($srp_config); // Load entity
        }

		$form[ '#tree' ] = TRUE;

		$form_state->setCached( FALSE );

		// General Description text to let the user know what they are doing.
		$form[ 'ucr_global_description' ] = array(
			'#markup' => t( 'Utilize this section to set up and maintain the global configurations of your website.' ),
		);

		// Setup for the vertical tabs view.
		$form[ 'ucr_global_config_tabs' ] = array(
			'#type' => 'vertical_tabs',
			'#title' => 'Global Configuration Settings:',
		);

		// Tab creation for the General Information Tab.
		$form[ 'ugc_item_general' ] = array(
			'#type' => 'details',
			'#title' => t( 'General Information' ),
			'#markup' => t('Please fill in all the general information fields provided.'),
			'#group' => 'ucr_global_config_tabs',
		);

		// Form elements for the General Information Tab.
		$form[ 'ugc_item_general' ][ 'organization_name' ] = [
			'#type' => 'textfield',
			'#title' => $this->t( 'Organization Name' ),
			'#description' => $this->t( 'Please enter in the Organization Name that your Dept, College, etc. belongs to.' ),
			'#required' => TRUE,
			'#default_value' => $ucr_config->get( 'organization_name' ),
		];

        // Form elements for the General Information Tab.
        $form[ 'ugc_item_general' ][ 'organization_url' ] = [
            '#type' => 'url',
            '#title' => $this->t( 'Organization URL' ),
            '#description' => $this->t( 'Please enter the URL to your Organization\'s main website. If it does not have one, leave the field blank.' ),
            '#required' => FALSE,
            '#default_value' => $ucr_config->get( 'organization_url' ),
        ];

        // Form elements for the General Information Tab.
        $form[ 'ugc_item_general' ][ 'show_global_links' ] = [
            '#type' => 'radios',
            '#title' => $this->t( 'Show UCR Footer Links' ),
            '#description' => $this->t( 'Please choose whether you want the standard-set UCR links to be displayed in the footer or not.' ),
            '#required' => FALSE,
            '#default_value' => $ucr_config->get( 'show_global_links' ),
            '#options' => array(0 => $this->t('Hide Links'), 1 => $this->t('Show Links')),
        ];

        // Form elements for the General Information Tab.
        $form[ 'ugc_item_general' ][ 'show_campus_map' ] = [
          '#type' => 'select',
          '#title' => $this->t( 'Campus Map Icon Display' ),
          '#description' => $this->t( 'Please choose how you want the campus map icon to be displayed within the footer below the Department Information.' ),
          '#required' => FALSE,
          '#default_value' => $ucr_config->get( 'show_campus_map' ),
          '#options' => array(0 => $this->t('Do not show anything'), 1 => $this->t("Show only the 'Find Us' Text"), 2 => $this->t("Show the full map icon")),
        ];

        // Tab creation for the Search Options.
        $form[ 'ugc_item_search_options' ] = array(
            '#type' => 'details',
            '#title' => t( 'Search Options' ),
            '#markup' => t('Please set the different search options for your website.'),
            '#group' => 'ucr_global_config_tabs',
        );

        // Form elements for the General Information Tab.
        $form[ 'ugc_item_search_options' ][ 'enable_site_search' ] = [
            '#type' => 'checkbox',
            '#title' => $this->t( '<strong>Enable Site-Only Search</strong>' ),
            '#description' => $this->t( 'If checked, visitors will have the option to search all of ucr.edu domain or only your site.' ),
            '#required' => FALSE,
            '#default_value' => $ucr_config->get( 'enable_site_search' ),
            //'#options' => array(0 => $this->t('Yes, Enable Site-Only Search')),
        ];

        // Form elements for the General Information Tab.
        $form[ 'ugc_item_search_options' ][ 'site_search_domain' ] = [
            '#type' => 'textfield',
            '#title' => $this->t( "Your Site's Search Domain Name" ),
            '#description' => $this->t( 'Please enter in the domain name (ex: sample.ucr.edu) that you want to have used as your site-only search.' ),
            '#required' => FALSE,
            '#default_value' => $ucr_config->get( 'site_search_domain' ),
        ];

        // Form elements for the General Information Tab.
        $form[ 'ugc_item_search_options' ][ 'search_results_page' ] = [
            '#type' => 'entity_autocomplete',
            '#target_type' => 'node',
            '#selection_settings' => array(
                'target_bundles' => array('gcs'),
            ),
            '#title' => $this->t( "Your Site's Search Results Page" ),
            '#description' => $this->t( "Please start typing in what the page name is for your search results page is. If none is provided, then the search results will use the ucr.edu's search results page."),
            '#required' => FALSE,
            '#default_value' => $search_results_page_entity,
        ];

		// Tab creation for the Dept. Contact Information.
		$form[ 'ugc_item_dept_info' ] = array(
			'#type' => 'details',
			'#title' => t( 'Department Contact Info' ),
			'#markup' => t('Please provide your Departments contact information.'),
			'#group' => 'ucr_global_config_tabs',
		);

		// Form elements for the Dept. Contact Information.
		$form[ 'ugc_item_dept_info' ][ 'dept_name' ] = array(
			'#type' => 'textfield',
			'#title' => $this->t( 'Department Name' ),
			'#required' => TRUE,
			'#default_value' => $ucr_config->get( 'dept_name' )
		);

		$form[ 'ugc_item_dept_info' ][ 'dept_address_1' ] = array(
			'#type' => 'textfield',
			'#title' => $this->t( 'Address' ),
			'#required' => TRUE,
			'#default_value' => $ucr_config->get( 'dept_address_1' )
		);

		$form[ 'ugc_item_dept_info' ][ 'dept_address_2' ] = array(
			'#type' => 'textfield',
			'#default_value' => $ucr_config->get( 'dept_address_2' )
		);

		$form[ 'ugc_item_dept_info' ][ 'dept_address_3' ] = array(
			'#type' => 'textfield',
			'#default_value' => $ucr_config->get( 'dept_address_3' )
		);

        $form['ugc_item_dept_info']['email_group'] = [
            '#type' => 'fieldset',
            '#title' => $this->t('Email'),
        ];
        $form['ugc_item_dept_info']['email_group']['primary_email'] = [
            '#type' => 'fieldset',
            '#title' => $this->t('Primary Email'),
        ];
        $form['ugc_item_dept_info']['email_group']['alternate_email'] = [
            '#type' => 'fieldset',
            '#title' => $this->t('Alternate Email'),
        ];
        $form['ugc_item_dept_info']['email_group']['tertiary_email'] = [
            '#type' => 'fieldset',
            '#title' => $this->t('Tertiary Email'),
        ];

        $form[ 'ugc_item_dept_info' ]['email_group']['primary_email'][ 'dept_custom_primary_email_label' ] = array(
            '#type' => 'textfield',
            '#title' => $this->t( 'Custom Primary Email Label' ),
            '#description' => $this->t("If left blank, 'email'  will be used."),
            '#default_value' => $ucr_config->get( 'dept_custom_primary_email_label' )
        );


        $form[ 'ugc_item_dept_info' ]['email_group']['primary_email'][ 'dept_primary_email' ] = array(
            '#type' => 'email',
            '#title' => $this->t( 'Primary Contact Email' ),
            '#default_value' => $ucr_config->get( 'dept_primary_email' )
        );

        $form[ 'ugc_item_dept_info' ]['email_group']['alternate_email'][ 'dept_custom_alternate_email_label' ] = array(
            '#type' => 'textfield',
            '#title' => $this->t( 'Custom Alternate Email Label' ),
            '#description' => $this->t("If left blank, 'alt email' will be used."),
            '#default_value' => $ucr_config->get( 'dept_custom_alternate_email_label' )
        );

        $form[ 'ugc_item_dept_info' ]['email_group']['alternate_email'][ 'dept_alternate_email' ] = array(
            '#type' => 'email',
            '#title' => $this->t( 'Alternate Contact Email' ),
            '#default_value' => $ucr_config->get( 'dept_alternate_email' )
        );

        $form[ 'ugc_item_dept_info' ]['email_group']['tertiary_email'][ 'dept_custom_tertiary_email_label' ] = array(
            '#type' => 'textfield',
            '#title' => $this->t( 'Custom Tertiary Email Label' ),
            '#description' => $this->t("If left blank, 'tertiary email' will be used."),
            '#default_value' => $ucr_config->get( 'dept_custom_tertiary_email_label' )
        );

        $form[ 'ugc_item_dept_info' ]['email_group']['tertiary_email'][ 'dept_tertiary_email' ] = array(
            '#type' => 'email',
            '#title' => $this->t( 'Tertiary Contact Email' ),
            '#default_value' => $ucr_config->get( 'dept_tertiary_email' )
        );

        $form['ugc_item_dept_info']['phone_group'] = [
            '#type' => 'fieldset',
            '#title' => $this->t('Telephone'),
        ];

        $form['ugc_item_dept_info']['phone_group']['primary_phone'] = [
            '#type' => 'fieldset',
            '#title' => $this->t('Primary Phone'),
        ];

        $form['ugc_item_dept_info']['phone_group']['alternate_phone'] = [
            '#type' => 'fieldset',
            '#title' => $this->t('Alternate Phone'),
        ];

        $form['ugc_item_dept_info']['phone_group']['fax'] = [
            '#type' => 'fieldset',
            '#title' => $this->t('Fax'),
        ];

        $form[ 'ugc_item_dept_info' ]['phone_group']['primary_phone'][ 'dept_custom_primary_phone_label' ] = array(
            '#type' => 'textfield',
            '#title' => $this->t( 'Custom Primary Phone Number Label' ),
            '#description' => $this->t("If left blank, 'tel' will be used"),
            '#default_value' => $ucr_config->get( 'dept_custom_primary_phone_label' )
        );

        $form[ 'ugc_item_dept_info' ]['phone_group']['primary_phone'][ 'dept_phone_1' ] = array(
            '#type' => 'tel',
            '#title' => $this->t( 'Primary Phone Number' ),
            '#default_value' => $ucr_config->get( 'dept_phone_1' )
        );

        $form[ 'ugc_item_dept_info' ]['phone_group']['alternate_phone'][ 'dept_custom_alternate_phone_label' ] = array(
            '#type' => 'textfield',
            '#title' => $this->t( 'Custom Alternate Phone Number Label' ),
            '#description' => $this->t("If left blank, 'alt tel' will be used."),
            '#default_value' => $ucr_config->get( 'dept_custom_alternate_phone_label' )
        );

        $form[ 'ugc_item_dept_info' ]['phone_group']['alternate_phone'][ 'dept_phone_2' ] = array(
            '#type' => 'tel',
            '#title' => $this->t( 'Alternate Phone Number' ),
            '#default_value' => $ucr_config->get( 'dept_phone_2' )
        );

        $form[ 'ugc_item_dept_info' ]['phone_group']['fax'][ 'dept_custom_fax_label' ] = array(
            '#type' => 'textfield',
            '#title' => $this->t( 'Custom Fax Number Label' ),
            '#description' => $this->t("If left blank, 'fax' will be used."),
            '#default_value' => $ucr_config->get( 'dept_custom_fax_label' )
        );

        $form[ 'ugc_item_dept_info' ]['phone_group']['fax'][ 'dept_phone_3' ] = array(
            '#type' => 'tel',
            '#title' => $this->t( 'Fax Number' ),
            '#default_value' => $ucr_config->get( 'dept_phone_3' )
        );

        $form[ 'ugc_item_dept_info' ][ 'dept_map_url' ] = array(
            '#type' => 'url',
            '#title' => $this->t( 'Campus Map URL' ),
            '#description' => $this->t('Please use the online campus map (<a href="https://campusmap.ucr.edu" target="_blank">https://campusmap.ucr.edu</a>) and provide the URL to your department location.'),
            '#required' => TRUE,
            '#default_value' => $ucr_config->get( 'dept_map_url' )
        );

		// Tab creation for the Social Links Information.
		$form[ 'ugc_item_social' ] = array(
			'#type' => 'details',
			'#title' => t( 'Social Links' ),
			'#markup' => t('Please provide any social links that you want to have available for visitors to your site to follow you. If you do not want the link to show up, leave the URL field blank.'),
			'#group' => 'ucr_global_config_tabs',
		);

		// Form elements for the Social Links.
        $form['ugc_item_social']['facebook_group'] = [
            '#type' => 'fieldset',
            '#title' => $this->t('Facebook'),
        ];

		$form['ugc_item_social']['facebook_group']['facebook_url'] = [
			'#type' => 'url',
			'#title' => $this->t('URL'),
			'#default_value' => $ucr_config->get('facebook_url'),
		];

        $form['ugc_item_social']['facebook_group']['facebook_title'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Title'),
            '#description' => $this->t('Provide a title that will display when a visitor hovers over the icon.'),
            '#default_value' => $ucr_config->get('facebook_title'),
        ];

        $form['ugc_item_social']['twitter_group'] = [
            '#type' => 'fieldset',
            '#title' => $this->t('Twitter'),
        ];

        $form['ugc_item_social']['twitter_group']['twitter_url'] = [
            '#type' => 'url',
            '#title' => $this->t('URL'),
            '#default_value' => $ucr_config->get('twitter_url'),
        ];

        $form['ugc_item_social']['twitter_group']['twitter_title'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Title'),
            '#description' => $this->t('Provide a title that will display when a visitor hovers over the icon.'),
            '#default_value' => $ucr_config->get('twitter_title'),
        ];

        $form['ugc_item_social']['youtube_group'] = [
            '#type' => 'fieldset',
            '#title' => $this->t('YouTube'),
        ];

        $form['ugc_item_social']['youtube_group']['youtube_url'] = [
            '#type' => 'url',
            '#title' => $this->t('URL'),
            '#default_value' => $ucr_config->get('youtube_url'),
        ];

        $form['ugc_item_social']['youtube_group']['youtube_title'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Title'),
            '#description' => $this->t('Provide a title that will display when a visitor hovers over the icon.'),
            '#default_value' => $ucr_config->get('youtube_title'),
        ];

        $form['ugc_item_social']['instagram_group'] = [
            '#type' => 'fieldset',
            '#title' => $this->t('Instagram'),
        ];

        $form['ugc_item_social']['instagram_group']['instagram_url'] = [
            '#type' => 'url',
            '#title' => $this->t('URL'),
            '#default_value' => $ucr_config->get('instagram_url'),
        ];

        $form['ugc_item_social']['instagram_group']['instagram_title'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Title'),
            '#description' => $this->t('Provide a title that will display when a visitor hovers over the icon.'),
            '#default_value' => $ucr_config->get('instagram_title'),
        ];

        $form['ugc_item_social']['linkedin_group'] = [
            '#type' => 'fieldset',
            '#title' => $this->t('Linkedin'),
        ];

        $form['ugc_item_social']['linkedin_group']['linkedin_url'] = [
            '#type' => 'url',
            '#title' => $this->t('URL'),
            '#default_value' => $ucr_config->get('linkedin_url'),
        ];

        $form['ugc_item_social']['linkedin_group']['linkedin_title'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Title'),
            '#description' => $this->t('Provide a title that will display when a visitor hovers over the icon.'),
            '#default_value' => $ucr_config->get('linkedin_title'),
        ];

        $form['ugc_item_social']['flickr_group'] = [
            '#type' => 'fieldset',
            '#title' => $this->t('Flickr'),
        ];

        $form['ugc_item_social']['flickr_group']['flickr_url'] = [
            '#type' => 'url',
            '#title' => $this->t('URL'),
            '#default_value' => $ucr_config->get('flickr_url'),
        ];

        $form['ugc_item_social']['flickr_group']['flickr_title'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Title'),
            '#description' => $this->t('Provide a title that will display when a visitor hovers over the icon.'),
            '#default_value' => $ucr_config->get('flickr_title'),
        ];

        $form['ugc_item_social']['rss_group'] = [
            '#type' => 'fieldset',
            '#title' => $this->t('RSS Feed'),
        ];

        $form['ugc_item_social']['rss_group']['rss_url'] = [
            '#type' => 'url',
            '#title' => $this->t('URL'),
            '#default_value' => $ucr_config->get('rss_url'),
        ];

        $form['ugc_item_social']['rss_group']['rss_title'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Title'),
            '#description' => $this->t('Provide a title that will display when a visitor hovers over the icon.'),
            '#default_value' => $ucr_config->get('rss_title'),
        ];

        $form['ugc_item_social']['reddit_group'] = [
          '#type' => 'fieldset',
          '#title' => $this->t('Reddit'),
        ];

        $form['ugc_item_social']['reddit_group']['reddit_url'] = [
          '#type' => 'url',
          '#title' => $this->t('URL'),
          '#default_value' => $ucr_config->get('reddit_url'),
        ];

        $form['ugc_item_social']['reddit_group']['reddit_title'] = [
          '#type' => 'textfield',
          '#title' => $this->t('Title'),
          '#description' => $this->t('Provide a title that will display when a visitor hovers over the icon.'),
          '#default_value' => $ucr_config->get('reddit_title'),
        ];

        $form['ugc_item_social']['discord_group'] = [
          '#type' => 'fieldset',
          '#title' => $this->t('Discord'),
        ];

        $form['ugc_item_social']['discord_group']['discord_url'] = [
          '#type' => 'url',
          '#title' => $this->t('URL'),
          '#default_value' => $ucr_config->get('discord_url'),
        ];

        $form['ugc_item_social']['discord_group']['discord_title'] = [
          '#type' => 'textfield',
          '#title' => $this->t('Title'),
          '#description' => $this->t('Provide a title that will display when a visitor hovers over the icon.'),
          '#default_value' => $ucr_config->get('discord_title'),
        ];

        $form['ugc_item_social']['tiktok_group'] = [
          '#type' => 'fieldset',
          '#title' => $this->t('TikTok'),
        ];

        $form['ugc_item_social']['tiktok_group']['tiktok_url'] = [
          '#type' => 'url',
          '#title' => $this->t('URL'),
          '#default_value' => $ucr_config->get('tiktok_url'),
        ];

        $form['ugc_item_social']['tiktok_group']['tiktok_title'] = [
          '#type' => 'textfield',
          '#title' => $this->t('Title'),
          '#description' => $this->t('Provide a title that will display when a visitor hovers over the icon.'),
          '#default_value' => $ucr_config->get('tiktok_title'),
        ];


    return parent::buildForm( $form, $form_state );
	}

	/**
	 * {@inheritdoc}
	 */
	public function validateForm( array & $form, FormStateInterface $form_state ) {
	    $values = $form_state->getValues();

        if(empty($values['ugc_item_general']['organization_name'])) {
            $form_state->setError($form['ugc_item_general']['organization_name'], $this->t('Please enter in the Organization Name.'));
        }

        if(!empty($values['ugc_item_general']['organization_url'])) {
            if(filter_var($values['ugc_item_general']['organization_url'], FILTER_VALIDATE_URL) == FALSE) {
                $form_state->setError($form['ugc_item_general']['organization_url'], $this->t('This is not a valid URL. Please enter in a valid URL.'));
            }
        }

        if(empty($values['ugc_item_dept_info']['dept_name'])) {
            $form_state->setError($form['ugc_item_dept_info']['dept_name'], $this->t('Please enter in your Departments Name.'));
        }

        if(!empty($values['ugc_item_dept_info']['email_group']['primary_email']['dept_primary_email'])) {
            if(filter_var($values['ugc_item_dept_info']['email_group']['primary_email']['dept_primary_email'], FILTER_VALIDATE_EMAIL) == FALSE) {
                $form_state->setError($form['ugc_item_dept_info']['email_group']['primary_email']['dept_primary_email'], $this->t('Please enter in a valid email address.'));
            }
        }

        if(!empty($values['ugc_item_dept_info']['email_group']['alternate_email']['dept_alternate_email'])) {
            if(filter_var($values['ugc_item_dept_info']['email_group']['alternate_email']['dept_alternate_email'], FILTER_VALIDATE_EMAIL) == FALSE) {
                $form_state->setError($form['ugc_item_dept_info']['email_group']['alternate_email']['dept_alternate_email'], $this->t('Please enter in a valid email address.'));
            }
        }

        if(!empty($values['ugc_item_dept_info']['email_group']['tertiary_email']['dept_tertiary_email'])) {
            if(filter_var($values['ugc_item_dept_info']['email_group']['tertiary_email']['dept_tertiary_email'], FILTER_VALIDATE_EMAIL) == FALSE) {
                $form_state->setError($form['ugc_item_dept_info']['email_group']['tertiary_email']['dept_tertiary_email'], $this->t('Please enter in a valid email address.'));
            }
        }

        if(!empty($values['ugc_item_social']['facebook_group']['facebook_url'])) {
            if(filter_var($values['ugc_item_social']['facebook_group']['facebook_url'], FILTER_VALIDATE_URL) == FALSE) {
                $form_state->setError($form['ugc_item_social']['facebook_group']['facebook_url'], $this->t('Please enter in a valid URL.'));
            }
        }

        if(!empty($values['ugc_item_social']['twitter_group']['twitter_url'])) {
            if(filter_var($values['ugc_item_social']['twitter_group']['twitter_url'], FILTER_VALIDATE_URL) == FALSE) {
                $form_state->setError($form['ugc_item_social']['twitter_group']['twitter_url'], $this->t('Please enter in a valid URL.'));
            }
        }

        if(!empty($values['ugc_item_social']['youtube_group']['youtube_url'])) {
            if(filter_var($values['ugc_item_social']['youtube_group']['youtube_url'], FILTER_VALIDATE_URL) == FALSE) {
                $form_state->setError($form['ugc_item_social']['youtube_group']['youtube_url'], $this->t('Please enter in a valid URL.'));
            }
        }

        if(!empty($values['ugc_item_social']['instagram_group']['instagram_url'])) {
            if(filter_var($values['ugc_item_social']['instagram_group']['instagram_url'], FILTER_VALIDATE_URL) == FALSE) {
                $form_state->setError($form['ugc_item_social']['instagram_group']['instagram_url'], $this->t('Please enter in a valid URL.'));
            }
        }

        if(!empty($values['ugc_item_social']['linkedin_group']['linkedin_url'])) {
            if(filter_var($values['ugc_item_social']['linkedin_group']['linkedin_url'], FILTER_VALIDATE_URL) == FALSE) {
                $form_state->setError($form['ugc_item_social']['linkedin_group']['linkedin_url'], $this->t('Please enter in a valid URL.'));
            }
        }

        if(!empty($values['ugc_item_social']['flickr_group']['flickr_url'])) {
            if(filter_var($values['ugc_item_social']['flickr_group']['flickr_url'], FILTER_VALIDATE_URL) == FALSE) {
                $form_state->setError($form['ugc_item_social']['flickr_group']['flickr_url'], $this->t('Please enter in a valid URL.'));
            }
        }

        if(!empty($values['ugc_item_social']['rss_group']['rss_url'])) {
            if(filter_var($values['ugc_item_social']['rss_group']['rss_url'], FILTER_VALIDATE_URL) == FALSE) {
                $form_state->setError($form['ugc_item_social']['rss_group']['rss_url'], $this->t('Please enter in a valid URL.'));
            }
        }

    if(!empty($values['ugc_item_social']['reddit_group']['reddit_url'])) {
      if(filter_var($values['ugc_item_social']['reddit_group']['reddit_url'], FILTER_VALIDATE_URL) == FALSE) {
        $form_state->setError($form['ugc_item_social']['reddit_group']['reddit_url'], $this->t('Please enter in a valid URL.'));
      }
    }

    if(!empty($values['ugc_item_social']['discord_group']['discord_url'])) {
      if(filter_var($values['ugc_item_social']['discord_group']['discord_url'], FILTER_VALIDATE_URL) == FALSE) {
        $form_state->setError($form['ugc_item_social']['discord_group']['discord_url'], $this->t('Please enter in a valid URL.'));
      }
    }

    if(!empty($values['ugc_item_social']['tiktok_group']['tiktok_url'])) {
      if(filter_var($values['ugc_item_social']['tiktok_group']['tiktok_url'], FILTER_VALIDATE_URL) == FALSE) {
        $form_state->setError($form['ugc_item_social']['tiktok_group']['tiktok_url'], $this->t('Please enter in a valid URL.'));
      }
    }
    }

	/**
	 * {@inheritdoc}
	 */
	public function submitForm( array & $form, FormStateInterface $form_state ) {
		$form_values = $form_state->getValues();

		$config_info = array(
			'organization_name' => $form_values[ 'ugc_item_general' ][ 'organization_name' ],
            'organization_url' => $form_values[ 'ugc_item_general' ][ 'organization_url' ],
            'show_global_links' => $form_values[ 'ugc_item_general' ][ 'show_global_links' ],
            'show_campus_map' => $form_values[ 'ugc_item_general' ][ 'show_campus_map' ],
            'enable_site_search' => $form_values[ 'ugc_item_search_options' ][ 'enable_site_search' ],
            'site_search_domain' => $form_values[ 'ugc_item_search_options' ][ 'site_search_domain' ],
            'search_results_page' => $form_values[ 'ugc_item_search_options' ][ 'search_results_page' ],
			'dept_name' => $form_values[ 'ugc_item_dept_info' ][ 'dept_name' ],
			'dept_address_1' => $form_values[ 'ugc_item_dept_info' ][ 'dept_address_1' ],
			'dept_address_2' => $form_values[ 'ugc_item_dept_info' ][ 'dept_address_2' ],
			'dept_address_3' => $form_values[ 'ugc_item_dept_info' ][ 'dept_address_3' ],
			'dept_primary_email' => $form_values[ 'ugc_item_dept_info' ]['email_group']['primary_email'][ 'dept_primary_email' ],
            'dept_alternate_email' => $form_values[ 'ugc_item_dept_info' ]['email_group']['alternate_email'][ 'dept_alternate_email' ],
            'dept_tertiary_email' => $form_values[ 'ugc_item_dept_info' ]['email_group']['tertiary_email'][ 'dept_tertiary_email' ],
            'dept_custom_primary_email_label' => $form_values[ 'ugc_item_dept_info' ]['email_group']['primary_email'][ 'dept_custom_primary_email_label' ],
            'dept_custom_alternate_email_label' => $form_values[ 'ugc_item_dept_info' ]['email_group']['alternate_email'][ 'dept_custom_alternate_email_label' ],
            'dept_custom_tertiary_email_label' => $form_values[ 'ugc_item_dept_info' ]['email_group']['tertiary_email'][ 'dept_custom_tertiary_email_label' ],
			'dept_phone_1' => $form_values[ 'ugc_item_dept_info' ]['phone_group']['primary_phone'][ 'dept_phone_1' ],
			'dept_phone_2' => $form_values[ 'ugc_item_dept_info' ]['phone_group']['alternate_phone'][ 'dept_phone_2' ],
            'dept_phone_3' => $form_values[ 'ugc_item_dept_info' ]['phone_group']['fax'][ 'dept_phone_3' ],
            'dept_custom_primary_phone_label' => $form_values[ 'ugc_item_dept_info' ]['phone_group']['primary_phone'][ 'dept_custom_primary_phone_label' ],
            'dept_custom_alternate_phone_label' => $form_values[ 'ugc_item_dept_info' ]['phone_group']['alternate_phone'][ 'dept_custom_alternate_phone_label' ],
            'dept_custom_fax_label' => $form_values[ 'ugc_item_dept_info' ]['phone_group']['fax'][ 'dept_custom_fax_label' ],
            'dept_map_url' => $form_values[ 'ugc_item_dept_info' ][ 'dept_map_url' ],
			'facebook_url' => $form_values[ 'ugc_item_social' ]['facebook_group'][ 'facebook_url' ],
            'facebook_title' => $form_values[ 'ugc_item_social' ]['facebook_group'][ 'facebook_title' ],
            'twitter_url' => $form_values[ 'ugc_item_social' ]['twitter_group'][ 'twitter_url' ],
            'twitter_title' => $form_values[ 'ugc_item_social' ]['twitter_group'][ 'twitter_title' ],
            'youtube_url' => $form_values[ 'ugc_item_social' ]['youtube_group'][ 'youtube_url' ],
            'youtube_title' => $form_values[ 'ugc_item_social' ]['youtube_group'][ 'youtube_title' ],
            'instagram_url' => $form_values[ 'ugc_item_social' ]['instagram_group'][ 'instagram_url' ],
            'instagram_title' => $form_values[ 'ugc_item_social' ]['instagram_group'][ 'instagram_title' ],
            'linkedin_url' => $form_values[ 'ugc_item_social' ]['linkedin_group'][ 'linkedin_url' ],
            'linkedin_title' => $form_values[ 'ugc_item_social' ]['linkedin_group'][ 'linkedin_title' ],
            'flickr_url' => $form_values[ 'ugc_item_social' ]['flickr_group'][ 'flickr_url' ],
            'flickr_title' => $form_values[ 'ugc_item_social' ]['flickr_group'][ 'flickr_title' ],
            'rss_url' => $form_values[ 'ugc_item_social' ]['rss_group'][ 'rss_url' ],
            'rss_title' => $form_values[ 'ugc_item_social' ]['rss_group'][ 'rss_title' ],
            'reddit_url' => $form_values['ugc_item_social'][ 'reddit_group' ][ 'reddit_url' ],
            'reddit_title' => $form_values['ugc_item_social']['reddit_group']['reddit_title'],
            'discord_url' => $form_values['ugc_item_social'][ 'discord_group' ][ 'discord_url' ],
            'discord_title' => $form_values['ugc_item_social']['discord_group']['discord_title'],
            'tiktok_url' => $form_values['ugc_item_social'][ 'tiktok_group' ][ 'tiktok_url' ],
            'tiktok_title' => $form_values['ugc_item_social']['tiktok_group']['tiktok_title'],
		);

		$config = $this->config( 'ucr_global.settings' );

		foreach ( $config_info as $key => $value ) {
			$config->set( $key, $value );
		}

		$config->save();

		parent::submitForm( $form, $form_state );
	}
}
