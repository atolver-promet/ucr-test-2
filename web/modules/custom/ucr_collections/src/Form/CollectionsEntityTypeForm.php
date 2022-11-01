<?php

namespace Drupal\ucr_collections\Form;

use Drupal\Component\Serialization\Yaml;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\Entity\EntityViewDisplay;
use Drupal\Core\Entity\Entity\EntityFormDisplay;
use Drupal\Core\Form\FormStateInterface;
use Drupal\taxonomy\Entity\Vocabulary;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\field\Entity\FieldConfig;

/**
 * Class CollectionsEntityTypeForm.
 */
class CollectionsEntityTypeForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $collections_type = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $collections_type->label(),
      '#description' => $this->t("Label for the Collection Item type."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $collections_type->id(),
      '#machine_name' => [
        'exists' => '\Drupal\ucr_collections\Entity\CollectionsEntityType::load',
      ],
      '#disabled' => !$collections_type->isNew(),
    ];

    /* You will need additional form elements for your custom properties. */

    $form['collection_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Collection Path'),
      '#maxlength' => 255,
      '#default_value' => $collections_type->get('collection_url'),
      '#description' => $this->t('Enter in the path that you want this collection to be viewed at.'),
      '#required' => TRUE,
    ];

    $form['collection_view'] = [
      '#type' => 'hidden',
      '#default_value' => $collections_type->get('collection_view'),
    ];

    $form['collection_category'] = [
      '#type' => 'hidden',
      '#default_value' => $collections_type->get('collection_category'),
    ];

    $form['collection_status'] = [
      '#type' => 'hidden',
      '#default_value' => $collections_type->get('collection_status'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   *
   * This is the default entity object builder function. It is called before any
   * other submit handler to build the new entity object to be used by the
   * following submit handlers. At this point of the form workflow the entity is
   * validated and the form state can be updated, this way the subsequently
   * invoked handlers can retrieve a regular entity object to act on. Generally
   * this method should not be overridden unless the entity requires the same
   * preparation for two actions, see \Drupal\comment\CommentForm for an example
   * with the save and preview actions.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Set the hidden field values only if this is a new bundle.
    $collection_view_id = 'collection__'.$form_state->getValue('id');
    $collection_view_id = (strlen($collection_view_id) > 32 ? substr($collection_view_id, 0, 32) : $collection_view_id);

    $collection_category_id = 'collection_category_'.$form_state->getValue('id');
    $collection_category_id = (strlen($collection_category_id) > 32 ? substr($collection_category_id, 0, 32) : $collection_category_id);

    $collection_status_id = 'collection_status_'.$form_state->getValue('id');
    $collection_status_id = (strlen($collection_status_id) > 32 ? substr($collection_status_id, 0, 32) : $collection_status_id);

    $form_state->setValue('collection_view', $collection_view_id);
    $form_state->setValue('collection_category', $collection_category_id);
    $form_state->setValue('collection_status', $collection_status_id);

    // Clean up the path variable
    $path_string = $form_state->getValue('collection_url');
    $path_string = strtolower(trim($path_string)); // Lowercase string and remove trailing white spaces.
    $path_string = str_replace(' ', '-', $path_string); // Replaces all spaces with hyphens.
    $path_string = preg_replace('/[^A-Za-z0-9\-]/', '', $path_string); // Removes special chars.
    $form_state->setValue('collection_url', $path_string);

    // Remove button and internal Form API values from submitted values.
    $form_state->cleanValues();
    $this->entity = $this->buildEntity($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $collections_type = $this->entity;
    $status = $collections_type->save();

    switch ($status) {
      case SAVED_NEW: {
        $this->messenger()->addMessage($this->t('Created the %label Collection Group.', [
          '%label' => $collections_type->label(),
        ]));

        // Create the Vocabulary Category for this new Collection Group
        $vocab_category = $collections_type->get('collection_category');
        $vocab_id = $collections_type->get('id');
        $vocab_name = 'Categories - ' . $collections_type->label();
        $vocab_description = 'A Taxonomy Vocabulary for Categorizations for the Collection Group: ' . $collections_type->label();
        $vocab_created = $this->createVocabulary($vocab_id, 'category', $vocab_category, $vocab_name, $vocab_description);
        if($vocab_created) {
          $this->messenger()->addMessage($this->t('Created the Collection Group Vocabulary for Categories.'));
        }

        // Create the Vocabulary Status for this new Collection Group
        $vocab_status = $collections_type->get('collection_status');
        $vocab_name = 'Status - ' . $collections_type->label();
        $vocab_description = 'A Taxonomy Vocabulary for Custom Status for the Collection Group: ' . $collections_type->label();
        $vocab_created = $this->createVocabulary($vocab_id, 'status', $vocab_status, $vocab_name, $vocab_description);
        if($vocab_created) {
          $this->messenger()->addMessage($this->t('Created the Collection Group Vocabulary for Status.'));
        }

        // ====== Add the necessary fields for the new Collection Group ====== //
        // Add the Category Field
        $field_storage = FieldStorageConfig::loadByName('collections', 'field_collection_item_categories');
        $field = FieldConfig::loadByName('collections', $collections_type->id(), 'field_collection_item_categories');
        if (empty($field)) {
          $field = FieldConfig::create([
            'field_storage' => $field_storage,
            'bundle' => $collections_type->id(),
            'entity_type' => 'collections',
            'field_type' => 'entity_reference',
            'label' => 'Categories',
            'description' => 'Select the Category(ies) that this item will show under.',
            'required' => TRUE,
            'settings' => [
              'handler' => 'default:taxonomy_term',
              'handler_settings' => [
                'target_bundles' => [$vocab_category => $vocab_category],
                'sort' => [
                  'field' => 'name',
                  'direction' => 'asc',
                ],
                'auto_create' => FALSE,
              ],
            ],
          ]);
          $field->save();
        }
        // Add the Status Field
        $field_storage = FieldStorageConfig::loadByName('collections', 'field_collection_item_status');
        $field = FieldConfig::loadByName('collections', $collections_type->id(), 'field_collection_item_status');
        if (empty($field)) {
          $field = FieldConfig::create([
            'field_storage' => $field_storage,
            'bundle' => $collections_type->id(),
            'entity_type' => 'collections',
            'field_type' => 'entity_reference',
            'label' => 'Effective Status',
            'description' => 'Select a Status to show in front of the title of this item.',
            'required' => FALSE,
            'settings' => [
              'handler' => 'default:taxonomy_term',
              'handler_settings' => [
                'target_bundles' => [$vocab_status => $vocab_status],
                'sort' => [
                  'field' => 'name',
                  'direction' => 'asc',
                ],
                'auto_create' => FALSE,
              ],
            ],
          ]);
          $field->save();
        }
        // Add the Date Field
        $field_storage = FieldStorageConfig::loadByName('collections', 'field_collection_item_date');
        $field = FieldConfig::loadByName('collections', $collections_type->id(), 'field_collection_item_date');
        if (empty($field)) {
          $field = FieldConfig::create([
            'field_storage' => $field_storage,
            'bundle' => $collections_type->id(),
            'entity_type' => 'collections',
            'field_type' => 'datetime',
            'label' => 'Effective Date',
            'description' => 'The date that this item is relevant. Will be used for organizing, lists, etc.',
            'required' => TRUE,
            'default_value' => [
              [
                'default_date_type' => 'now',
                'default_date' => 'now',
              ],
            ],
          ]);
          $field->save();
        }
        // Add the General Field
        $field_storage = FieldStorageConfig::loadByName('collections', 'field_collection_item_general');
        $field = FieldConfig::loadByName('collections', $collections_type->id(), 'field_collection_item_general');
        if (empty($field)) {
          $field = FieldConfig::create([
            'field_storage' => $field_storage,
            'bundle' => $collections_type->id(),
            'entity_type' => 'collections',
            'field_type' => 'text_long',
            'label' => 'General',
            'description' => '',
            'required' => FALSE,
          ]);
          $field->save();
        }
        // Add the Media Field
        $field_storage = FieldStorageConfig::loadByName('collections', 'field_collection_item_media');
        $field = FieldConfig::loadByName('collections', $collections_type->id(), 'field_collection_item_media');
        if (empty($field)) {
          $field = FieldConfig::create([
            'field_storage' => $field_storage,
            'bundle' => $collections_type->id(),
            'entity_type' => 'collections',
            'field_type' => 'entity_reference',
            'label' => 'Media',
            'description' => '',
            'required' => FALSE,
            'settings' => [
              'handler' => 'default:media',
              'handler_settings' => [
                'target_bundles' => [
                  'document' => 'document',
                  'image' => 'image',
                  'video' => 'video',
                ],
                'sort' => [
                  'field' => 'name',
                  'direction' => 'asc',
                ],
                'auto_create' => FALSE,
                'auto_create_bundle' => 'document',
              ],
            ],
          ]);
          $field->save();
        }
        // Add the URL Field
        $field_storage = FieldStorageConfig::loadByName('collections', 'field_collection_item_urls');
        $field = FieldConfig::loadByName('collections', $collections_type->id(), 'field_collection_item_urls');
        if (empty($field)) {
          $field = FieldConfig::create([
            'field_storage' => $field_storage,
            'bundle' => $collections_type->id(),
            'entity_type' => 'collections',
            'field_type' => 'link',
            'label' => 'URL\'s',
            'description' => '',
            'required' => FALSE,
            'settings' => [
              'link_type' => 17,
              'title' => 2,
            ],
          ]);
          $field->save();
        }

        // Set the Workflow for this new Collection Group
        /** @var \Drupal\workflows\WorkflowInterface $workflow */
        $workflow = $this->entityTypeManager->getStorage("workflow")->loadByProperties(["id" => "editorial"])["editorial"];
        $config = $workflow->getTypePlugin()->getConfiguration();
        $config["entity_types"]["collections"][] = $collections_type->id();
        $workflow->getTypePlugin()->setConfiguration($config);
        $workflow->save();

        // Create the Form Display View for the new Collection Group
        $storage = \Drupal::entityTypeManager()->getStorage('entity_form_display');
        $form_display = $storage->load('collections.' . $collections_type->get('id') . '.default');
        if ($form_display === NULL) {
          $form_display = $storage->create([
            'targetEntityType' => 'collections',
            'bundle' => $collections_type->get('id'),
            'mode' => 'default',
            'status' => TRUE,
            'third_party_settings' => [
              'field_group' => [
                'group_content_tabs' => [
                  'children' => [
                    'group_general',
                    'group_media',
                    'group_urls'
                  ],
                  'parent_name' => '',
                  'weight' => 4,
                  'format_type' => 'tabs',
                  'format_settings' => [
                    'id' => '',
                    'classes' => '',
                    'direction' => 'horizontal',
                  ],
                  'label' => 'Content Tabs',
                  'region' => 'content',
                ],
                'group_general' => [
                  'children' => [
                    'field_collection_item_general',
                  ],
                  'parent_name' => 'group_content_tabs',
                  'weight' => 5,
                  'format_type' => 'tab',
                  'format_settings' => [
                    'id' => '',
                    'classes' => '',
                    'description' => '',
                    'formatter' => 'open',
                    'required_fields' => true,
                  ],
                  'label' => 'General',
                  'region' => 'content',
                ],
                'group_media' => [
                  'children' => [
                    'field_collection_item_media',
                  ],
                  'parent_name' => 'group_content_tabs',
                  'weight' => 6,
                  'format_type' => 'tab',
                  'format_settings' => [
                    'id' => '',
                    'classes' => '',
                    'description' => '',
                    'formatter' => 'closed',
                    'required_fields' => true,
                  ],
                  'label' => 'Media',
                  'region' => 'content',
                ],
                'group_urls' => [
                  'children' => [
                    'field_collection_item_urls',
                  ],
                  'parent_name' => 'group_content_tabs',
                  'weight' => 7,
                  'format_type' => 'tab',
                  'format_settings' => [
                    'id' => '',
                    'classes' => '',
                    'description' => '',
                    'formatter' => 'closed',
                    'required_fields' => true,
                  ],
                  'label' => 'URL\'s',
                  'region' => 'content',
                ],
              ],
            ],
          ]);
        }

        $form_display->removeComponent('user_id')
          ->setComponent('name', [
            'type' => 'string_textfield',
            'weight' => 0,
            'settings' => [
              'size' => 60,
            ],
          ])
          ->setComponent('field_collection_item_date', [
            'type' => 'datetime_default',
            'weight' => 1,
          ])
          ->setComponent('field_collection_item_status', [
            'type' => 'options_select',
            'weight' => 2,
          ])
          ->setComponent('field_collection_item_categories', [
            'type' => 'entity_reference_autocomplete',
            'weight' => 3,
            'settings' => [
              'match_operator' => 'CONTAINS',
              'size' => 60,
            ],
          ])
          ->setComponent('field_collection_item_general', [
            'type' => 'text_textarea',
            'weight' => 8,
            'settings' => [
              'rows' => 5,
            ],
          ])
          ->setComponent('field_collection_item_media', [
            'type' => 'media_library_widget',
            'weight' => 9,
          ])
          ->setComponent('field_collection_item_urls', [
            'type' => 'link_attributes',
            'weight' => 10,
            'settings' => [
              'enabled_attributes' => [
                'target' => true,
                'rel' => true,
                'class' => true,
              ],
            ],
          ])
          ->save();

        // Create the View Display View for the new Collection Group
        $storage = \Drupal::entityTypeManager()->getStorage('entity_view_display');
        $view_display = $storage->load('collections.' . $collections_type->get('id') . '.default');
        if ($view_display === NULL) {
          $view_display = $storage->create([
            'targetEntityType' => 'collections',
            'bundle' => $collections_type->get('id'),
            'mode' => 'default',
            'status' => TRUE,
          ]);
        }

        $view_display->removeComponent('search_api_excerpt')
          ->removeComponent('user_id')
          ->setComponent('name', [
            'type' => 'string',
            'weight' => 0,
            'label' => 'hidden',
            'settings' => [
              'link_to_entity' => false,
            ],
          ])
          ->setComponent('field_collection_item_categories', [
            'type' => 'entity_reference_entity_id',
            'weight' => 1,
            'label' => 'hidden',
          ])
          ->setComponent('field_collection_item_status', [
            'type' => 'entity_reference_entity_id',
            'weight' => 2,
            'label' => 'hidden',
          ])
          ->setComponent('field_collection_item_date', [
            'type' => 'datetime_default',
            'weight' => 3,
            'label' => 'hidden',
            'settings' => [
              'format_type' => 'medium',
            ],
          ])
          ->setComponent('field_collection_item_general', [
            'type' => 'text_default',
            'weight' => 4,
            'label' => 'hidden',
            'settings' => [
              'format_type' => 'medium',
            ],
          ])
          ->setComponent('field_collection_item_media', [
            'type' => 'entity_reference_entity_id',
            'weight' => 5,
            'label' => 'hidden',
          ])
          ->setComponent('field_collection_item_urls', [
            'type' => 'link',
            'weight' => 6,
            'label' => 'hidden',
            'settings' => [
              'trim_length' => null,
              'url_only' => false,
              'url_plain' => false,
              'rel' => '0',
              'target' => '0',
            ],
          ])
          ->save();

        // Add the code for creating the View as needed.
        $this->createView($collections_type->get('id'), $collections_type->label(), $collections_type->get('collection_category'), $collections_type->get('collection_view'), $collections_type->get('collection_url'));
      break;
      }
      default: {
        $this->updateView($collections_type->get('collection_view'), $collections_type->get('collection_url'));
        $this->messenger()->addMessage($this->t('Saved the %label Collection Group.', [
          '%label' => $collections_type->label(),
        ]));
      break;
      }
    }

    $form_state->setRedirectUrl($collections_type->toUrl('collection'));
  }

  // Primary function used to create the views and set the proper configuration settings.
  protected function createView($collection_type_id, $collection_type_name, $category_id, $collection_view_id, $collection_path_url) {
    $file_path = DRUPAL_ROOT . '/modules/custom/ucr_collections/view_templates/view_template.yml';
    $file_contents = file_get_contents($file_path);
    $page_template = Yaml::decode($file_contents);
    $page_template['dependencies']['config'][] = 'taxonomy.vocabulary.'.$category_id;
    $page_template['dependencies']['config'][] = 'ucr_collections.collections_type.'.$collection_type_id;
    $page_template['id'] = $collection_view_id;
    $page_template['label'] = $collection_type_name.' View';
    $page_template['display']['default']['display_options']['filters']['type']['value'] = [$collection_type_id => $collection_type_id];
    $page_template['display']['default']['display_options']['filters']['field_collection_item_categories_target_id']['vid'] = $category_id;
    $page_template['display']['default']['display_options']['title'] = $collection_type_name;
    $page_template['display']['default_page']['display_options']['path'] = $collection_path_url;

    $config = \Drupal::configFactory();
    $base_view = $config->getEditable('views.view.'.$collection_view_id);
    if($base_view->isNew()) {
      $base_view->setData($page_template)->save(TRUE);
      $this->messenger()->addMessage($this->t('Created the Collection View.'));
    } else {
      $this->messenger()->addMessage($this->t('Unable to create the Collection View, as the machine name for it already exists. Please contact support.'));
    }
  }

  // Update of the view, which will only update the path.
  protected function updateView($collection_view_id, $collection_path_url) {
    $config = \Drupal::configFactory();
    $base_view = $config->getEditable('views.view.'.$collection_view_id);
    if(!$base_view->isNew()) {
      $base_view->set('display.default_page.display_options.path', $collection_path_url)->save(TRUE);
      $this->messenger()->addMessage($this->t('Updated the Collection View.'));
    } else {
      $this->messenger()->addMessage($this->t('Unable to update the Collection View, as it did not exist already. Please contact support.'));
    }
  }

  protected function createVocabulary($collection_id, $vocab_type, $bundle_id, $vocab_name, $vocab_description) {
    $vocabulary = Vocabulary::load($bundle_id);
    if (!$vocabulary) {
      $vocabulary = Vocabulary::create([
        'vid' => $bundle_id,
        'description' => $vocab_description,
        'name' => $vocab_name,
      ]);
      $vocab_status = $vocabulary->save();
      switch ($vocab_status) {
        case SAVED_NEW: {
          // Create / Update the Display View of the Taxonomy.
          $storage = \Drupal::entityTypeManager()->getStorage('entity_view_display');
          $view_display = $storage->load('taxonomy_term.' . $bundle_id . '.default');
          if ($view_display === NULL) {
            $view_display = $storage->create([
              'targetEntityType' => 'taxonomy_term',
              'bundle' => $bundle_id,
              'mode' => 'default',
              'status' => TRUE,
            ]);
          }

          $view_display->removeComponent('description')
            ->removeComponent('search_api_excerpt')
            ->save();

          // Create / Update the Form View of the Taxonomy.
          $storage = \Drupal::entityTypeManager()->getStorage('entity_form_display');
          $form_display = $storage->load('taxonomy_term.' . $bundle_id . '.default');
          if ($form_display === NULL) {
            $form_display = $storage->create([
              'targetEntityType' => 'taxonomy_term',
              'bundle' => $bundle_id,
              'mode' => 'default',
              'status' => TRUE,
            ]);
          }

          $form_display->removeComponent('description')
            ->save();

          // Create the Pathauto Pattern for the Vocabulary
          $path_id = 'vocab_path_'.$collection_id.'_'.$vocab_type;
          $path_id = (strlen($path_id) > 32 ? substr($path_id, 0, 32) : $path_id);
          $data = [
            'id' => $path_id,
            'label' => 'Collection Group '.ucwords($vocab_type),
            'type' => 'canonical_entities:taxonomy_term',
            'pattern' => '/collections/'.$vocab_type.'/'.str_replace('_', '-', $collection_id).'/[term:name]',
            'weight' => -5,
          ];
          $pattern = \Drupal::entityTypeManager()->getStorage('pathauto_pattern')->create($data);
          // Add the bundle condition.
          $pattern->addSelectionCondition([
            'id' => 'entity_bundle:taxonomy_term',
            'bundles' => [$bundle_id => $bundle_id],
            'negate' => FALSE,
            'context_mapping' => ['taxonomy_term' => 'taxonomy_term'],
          ]);

          $pattern->save();

          return true;
          break;
        }
        default: {
          return false;
        }
      }
    }
    return true;
  }
}
