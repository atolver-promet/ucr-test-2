<?php

namespace Drupal\ucr_collections\Form;

use Drupal\Driver\Exception\Exception;
use Drupal\feeds\Feeds\Target\Path;
use Drupal\pathauto\Entity\PathautoPattern;
use Drupal\taxonomy\Entity\Vocabulary;
use Drupal\Core\Entity\EntityConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Builds the form to delete Collection Item type entities.
 */
class CollectionsEntityTypeDeleteForm extends EntityConfirmFormBase {

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to delete %name?', ['%name' => $this->entity->label()]);
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->t('This will delete the associated Vocabularies and Paths. This action cannot be undone.');
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('entity.collections_type.collection');
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $num_nodes = $this->entityTypeManager->getStorage('collections')->getQuery()
      ->condition('type', $this->entity->id())
      ->count()
      ->execute();
    if ($num_nodes) {
      $caption = '<p>' . $this->formatPlural($num_nodes, '%type is used by 1 piece of content on your site. You can not remove this collection group until you have removed all of the %type content.', '%type is used by @count pieces of content on your site. You may not remove %type until you have removed all of the %type content.', ['%type' => $this->entity->label()]) . '</p>';
      $form['#title'] = $this->getQuestion();
      $form['description'] = ['#markup' => $caption];
      return $form;
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $collection_category = $this->entity->get('collection_category');
    if($collection_category) { $this->deleteVocabulary($collection_category, 'category'); }

    $collection_status = $this->entity->get('collection_status');
    if($collection_status) { $this->deleteVocabulary($collection_status, 'status'); }

    $collection_view = $this->entity->get('collection_view');
    if($collection_view) { $this->deleteView($collection_view); }

    $this->entity->delete();

    // Set the Workflow for this new Collection Group
    /** @var \Drupal\workflows\WorkflowInterface $workflow */
    $workflow = $this->entityTypeManager->getStorage("workflow")->loadByProperties(["id" => "editorial"])["editorial"];
    $config = $workflow->getTypePlugin()->getConfiguration();
    unset($config["entity_types"]["collections"][$this->entity->id()]);
    $workflow->getTypePlugin()->setConfiguration($config);
    $workflow->save();

    $this->messenger()->addMessage(
      $this->t('The Collection Group @label has been deleted.', [
        '@label' => $this->entity->label(),
      ])
    );

    $form_state->setRedirectUrl($this->getCancelUrl());
  }

  protected function deleteVocabulary($vid, $vocab_type) {
    $vocab = Vocabulary::load($vid);
    if($vocab) {
      $tids = \Drupal::entityQuery('taxonomy_term')
        ->condition('vid', $vid)
        ->execute();

      $controller = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
      $entities = $controller->loadMultiple($tids);
      $controller->delete($entities);

      $vocab->delete();

      // Remove the Pathauto Pattern.
      $path_id = 'vocab_path_'.$this->entity->id().'_'.$vocab_type;
      $path_id = (strlen($path_id) > 32 ? substr($path_id, 0, 32) : $path_id);
      if($alias_entity = PathautoPattern::load($path_id)) {
        try {
          $alias_entity->delete();
        } catch(Exception $e) {
          \Drupal::logger('ucr_collections')->error('Encountered the following error while trying to delete the @id path alias pattern: @error',
            array(
              '@id'=>$path_id,
              '@error'=>$e->getMessage(),
            )
          );
        }
      }
    }
  }

  protected function deleteView($view_id) {
    \Drupal::configFactory()->getEditable('views.view.'.$view_id)->delete();
  }
}
