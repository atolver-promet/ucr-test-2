<?php

/**
 * @file
 * contains landing page functionality for Lightning
 */

namespace Drupal\ucr_scotmemo\Plugin\WebformHandler;

use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Serialization\Yaml;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Drupal\file\Entity\File;
use Drupal\media\Entity\Media;
use Drupal\webform\WebformInterface;
use Drupal\webform\Plugin\WebformHandlerBase;
use Drupal\webform\webformSubmissionInterface;
use Drupal\webform\Entity\WebformSubmission;
use Drupal\taxonomy\Entity\Term;


/**
 * Create a new node entity from a webform submission.
 *
 * @WebformHandler(
 *   id = "Create a node",
 *   label = @Translation("Create a ScotMemo Submission"),
 *   category = @Translation("Entity Creation"),
 *   description = @Translation("Creates a new node of the ScotMemo Submissions Content Type from ScotMemo Webform Submissions."),
 *   cardinality = \Drupal\webform\Plugin\WebformHandlerInterface::CARDINALITY_UNLIMITED,
 *   results = \Drupal\webform\Plugin\WebformHandlerInterface::RESULTS_PROCESSED,
 *   submission = \Drupal\webform\Plugin\WebformHandlerInterface::SUBMISSION_REQUIRED,
 * )
 */

class NewsWebformHandler extends WebformHandlerBase {

    public function submitForm(array &$form, FormStateInterface $form_state, WebformSubmissionInterface $webform_submission){

        //Get array of form submitted values
        $submission_array = $webform_submission->getData();

        //prepare variables to be used
        $scotmemo_title  = $submission_array['scotmemo_title'];
        $scotmemo_submit_on_behalf_of = $submission_array['submit_on_behalf_of'];
        $scotmemo_content = $submission_array['scotmemo_content']['value'];
        $scotmemo_image_header = $submission_array['scotmemo_image_header'];
        $scotmemo_image_align_left = $submission_array['left_align_image'];
        $scotmemo_date_to_publish = $submission_array['scotmemo_date_to_publish'];
        $image_uploaded = ($scotmemo_image_header ? true:false);


        if($image_uploaded){
             $file = File::load($scotmemo_image_header);
             $file->setPermanent();
             $file->save();

                 $image_media = Media::create([
                     'bundle' => 'image',
                     'uid' => \Drupal::currentUser()->id(),
                     'langcode' => \Drupal::languageManager()->getDefaultLanguage()->getId(),
                     'status' => true,
                     'image' => [
                         'target_id' => $file->id(),
                         'alt' => $file->getFilename(),
                     ],
                 ]);
                 $image_media->save();
         }
         
        $summary = substr(strip_tags($scotmemo_content), 0, 100);
        // Create the node
        $node = Node::create([
            'type' => 'scotmemo_submissions',
            'title' => $scotmemo_title,
            'langcode' => 'en',
            'uid' => \Drupal::currentUser()->id(),
            'body' => [
                'summary' => $summary,
                'value' => $scotmemo_content,
                'format' => 'rich_text_editor_limited',
            ],
            'field_memo_header_image' => [
                    'target_id' => ($image_uploaded ? $image_media->  id():"" ),
            ],
            'field_left_align_this_image' =>[
                'value' => $scotmemo_image_align_left,
            ],

            'field_submitting_on_behalf_of' => [
              'target_id' => $scotmemo_submit_on_behalf_of,
            ],
            'field_date_to_publish' => [
                'value' => $scotmemo_date_to_publish,
            ],
        ]);

        $node -> save();
    }

}