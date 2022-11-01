<?php

namespace Drupal\ucr_collections\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Datetime\DateFormatter;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Render\Renderer;
use Drupal\Core\Url;
use Drupal\ucr_collections\Entity\CollectionsEntityInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class CollectionsEntityController.
 *
 *  Returns responses for Collection Item routes.
 */
class CollectionsEntityController extends ControllerBase implements ContainerInjectionInterface {


  /**
   * The date formatter.
   *
   * @var \Drupal\Core\Datetime\DateFormatter
   */
  protected $dateFormatter;

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\Renderer
   */
  protected $renderer;

  /**
   * Constructs a new CollectionsEntityController.
   *
   * @param \Drupal\Core\Datetime\DateFormatter $date_formatter
   *   The date formatter.
   * @param \Drupal\Core\Render\Renderer $renderer
   *   The renderer.
   */
  public function __construct(DateFormatter $date_formatter, Renderer $renderer) {
    $this->dateFormatter = $date_formatter;
    $this->renderer = $renderer;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('date.formatter'),
      $container->get('renderer')
    );
  }

  /**
   * Displays a Collection Item revision.
   *
   * @param int $collections_revision
   *   The Collection Item revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($collections_revision) {
    $collections = $this->entityTypeManager()->getStorage('collections')
      ->loadRevision($collections_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('collections');

    return $view_builder->view($collections);
  }

  /**
   * Page title callback for a Collection Item revision.
   *
   * @param int $collections_revision
   *   The Collection Item revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($collections_revision) {
    $collections = $this->entityTypeManager()->getStorage('collections')
      ->loadRevision($collections_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $collections->label(),
      '%date' => $this->dateFormatter->format($collections->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a Collection Item.
   *
   * @param \Drupal\ucr_collections\Entity\CollectionsEntityInterface $collections
   *   A Collection Item object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(CollectionsEntityInterface $collections) {
    $account = $this->currentUser();
    $collections_storage = $this->entityTypeManager()->getStorage('collections');

    $langcode = $collections->language()->getId();
    $langname = $collections->language()->getName();
    $languages = $collections->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $collections->label()]) : $this->t('Revisions for %title', ['%title' => $collections->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all collection item revisions") || $account->hasPermission('administer collection item entities')));
    $delete_permission = (($account->hasPermission("delete all collection item revisions") || $account->hasPermission('administer collection item entities')));

    $rows = [];

    $vids = $collections_storage->revisionIds($collections);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\ucr_collections\CollectionsEntityInterface $revision */
      $revision = $collections_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $collections->getRevisionId()) {
          $link = $this->l($date, new Url('entity.collections.revision', [
            'collections' => $collections->id(),
            'collections_revision' => $vid,
          ]));
        }
        else {
          $link = $collections->link($date);
        }

        $row = [];
        $column = [
          'data' => [
            '#type' => 'inline_template',
            '#template' => '{% trans %}{{ date }} by {{ username }}{% endtrans %}{% if message %}<p class="revision-log">{{ message }}</p>{% endif %}',
            '#context' => [
              'date' => $link,
              'username' => $this->renderer->renderPlain($username),
              'message' => [
                '#markup' => $revision->getRevisionLogMessage(),
                '#allowed_tags' => Xss::getHtmlTagList(),
              ],
            ],
          ],
        ];
        $row[] = $column;

        if ($latest_revision) {
          $row[] = [
            'data' => [
              '#prefix' => '<em>',
              '#markup' => $this->t('Current revision'),
              '#suffix' => '</em>',
            ],
          ];
          foreach ($row as &$current) {
            $current['class'] = ['revision-current'];
          }
          $latest_revision = FALSE;
        }
        else {
          $links = [];
          if ($revert_permission) {
            $links['revert'] = [
              'title' => $this->t('Revert'),
              'url' => $has_translations ?
              Url::fromRoute('entity.collections.translation_revert', [
                'collections' => $collections->id(),
                'collections_revision' => $vid,
                'langcode' => $langcode,
              ]) :
              Url::fromRoute('entity.collections.revision_revert', [
                'collections' => $collections->id(),
                'collections_revision' => $vid,
              ]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.collections.revision_delete', [
                'collections' => $collections->id(),
                'collections_revision' => $vid,
              ]),
            ];
          }

          $row[] = [
            'data' => [
              '#type' => 'operations',
              '#links' => $links,
            ],
          ];
        }

        $rows[] = $row;
      }
    }

    $build['collections_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
