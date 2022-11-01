<?php

namespace Drupal\ucr_collections\Form;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Datetime\DateFormatter;
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element\Date;
use Drupal\Core\Session\AccountProxyInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form controller for Collection Item edit forms.
 *
 * @ingroup ucr_collections
 */
class CollectionsEntityForm extends ContentEntityForm {

  /**
   * The current user account.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $account;
  protected $dateformatter;

  /**
   * Constructs a new CollectionsEntityForm.
   *
   * @param \Drupal\Core\Entity\EntityRepositoryInterface $entity_repository
   *   The entity repository service.
   * @param \Drupal\Core\Entity\EntityTypeBundleInfoInterface $entity_type_bundle_info
   *   The entity type bundle service.
   * @param \Drupal\Component\Datetime\TimeInterface $time
   *   The time service.
   * @param \Drupal\Core\Session\AccountProxyInterface $account
   *   The current user account.
   */
  public function __construct(EntityRepositoryInterface $entity_repository, EntityTypeBundleInfoInterface $entity_type_bundle_info = NULL, TimeInterface $time = NULL, AccountProxyInterface $account) {
    parent::__construct($entity_repository, $entity_type_bundle_info, $time);

    $this->account = $account;
    $this->dateformatter = \Drupal::service('date.formatter');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    return new static(
      $container->get('entity.repository'),
      $container->get('entity_type.bundle.info'),
      $container->get('datetime.time'),
      $container->get('current_user')
    );
  }

  public function form(array $form, FormStateInterface $form_state) {
    /** @var \Drupal\ucr_collections\Entity\CollectionsEntityInterface $collection_item */
    $collection_item = $this->entity;

    if ($this->operation == 'edit') {
      $form['#title'] = $this->t('<em>Edit @type</em> @title', [
        '@type' => ucr_collections_get_type_label($collection_item),
        '@title' => $collection_item->label(),
      ]);
    }

    // Changed must be sent to the client, for later overwrite error checking.
    $form['changed'] = [
      '#type' => 'hidden',
      '#default_value' => $collection_item->getChangedTime(),
    ];

    $form = parent::form($form, $form_state);

    $form['#theme'] = ['node_edit_form'];
    $form['#attached']['library'][] = 'seven/node-form';
    $form['#attached']['library'][] = 'node/form';

    $form['advanced'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['entity-meta']]
    ];

    $form['meta'] = [
      '#type' => 'container',
      '#access' => TRUE,
      '#attributes' => ['class' => ['entity-meta__header']],
    ];

    $form['meta']['author'] = [
      '#type' => 'item',
      '#title' => $this->t('Author'),
      '#markup' => $collection_item->getOwner()->getAccountName(),
      '#wrapper_attributes' => ['class' => ['entity-meta__author', 'container-inline']],
    ];

    $form['meta']['changed'] = [
      '#type' => 'item',
      '#title' => $this->t('Last saved'),
      '#markup' => !$collection_item->isNew() ? $this->dateformatter->format($collection_item->getChangedTime(), 'short') : $this->t('Not saved yet'),
      '#wrapper_attributes' => ['class' => ['entity-meta__last-saved', 'container-inline']],
    ];

    $form['revision_information']['#type'] = 'container';
    $form['revision_information']['#group'] = 'meta';

    $form['field_collection_item_date']['#group'] = 'meta';

    $form['meta']['#group'] = 'advanced';

    $form['status']['#weight'] = 100;
    $form['status']['#group'] = 'footer';

    $form['collection_status'] = [
      '#type' => 'details',
      '#title' => t('Effective Status'),
      '#group' => 'advanced',
      '#attributes' => [
        'class' => ['node-form-options']
      ],
      '#attached' => [
        'library' => ['node/drupal.node'],
      ],
      '#weight' => 101,
      '#optional' => true,
      '#open' => false,
    ];

    $form['field_collection_item_status']["#group"] = 'collection_status';

    $form['collection_category'] = [
      '#type' => 'details',
      '#title' => t('Item Categories'),
      '#group' => 'advanced',
      '#attributes' => [
        'class' => ['node-form-options']
      ],
      '#attached' => [
        'library' => ['node/drupal.node'],
      ],
      '#weight' => 102,
      '#optional' => true,
      '#open' => false,
    ];

    $form['field_collection_item_categories']["#group"] = 'collection_category';

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var \Drupal\ucr_collections\Entity\CollectionsEntity $entity */
    $form = parent::buildForm($form, $form_state);

    if (!$this->entity->isNew()) {
      $form['new_revision'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Create new revision'),
        '#default_value' => FALSE,
        '#weight' => 10,
      ];
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;

    // Save as a new revision if requested to do so.
    if (!$form_state->isValueEmpty('new_revision') && $form_state->getValue('new_revision') != FALSE) {
      $entity->setNewRevision();

      // If a new revision is created, save the current user as revision author.
      $entity->setRevisionCreationTime($this->time->getRequestTime());
      $entity->setRevisionUserId($this->account->id());
    }
    else {
      $entity->setNewRevision(FALSE);
    }

    $status = parent::save($form, $form_state);

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Collection Item.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label Collection Item.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect('entity.collections.canonical', ['collections' => $entity->id()]);
  }

}
