<?php

namespace Drupal\og_accelerator\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\og_accelerator\Entity\ItemEntityInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines a controller to render item entity operations.
 */
class ItemEntityController extends ControllerBase implements ContainerInjectionInterface
{
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
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container)
  {
    $instance = parent::create($container);
    $instance->dateFormatter = $container->get('date.formatter');
    $instance->renderer = $container->get('renderer');
    return $instance;
  }

  /**
   * Displays a Item revision.
   *
   * @param int $item_entity_revision
   *   The Item revision ID.
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($item_entity_revision)
  {
    $item_entity = $this->entityTypeManager()->getStorage('item_entity')->loadRevision($item_entity_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('item_entity');
    return $view_builder->view($item_entity);
  }

  /**
   * Page title callback for a Item revision.
   *
   * @param int $item_entity_revision
   *   The Item revision ID.
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($item_entity_revision)
  {
    /** @var \Drupal\og_accelerator\Entity\ItemEntity $item_entity */
    $item_entity = $this->entityTypeManager()->getStorage('item_entity')->loadRevision($item_entity_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $item_entity->label(),
      '%date' => $this->dateFormatter->format($item_entity->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a Item.
   *
   * @param \Drupal\og_accelerator\Entity\ItemEntityInterface $item_entity
   *   An Item object.
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(ItemEntityInterface $item_entity)
  {
    $account = $this->currentUser();
    /** @var \Drupal\og_accelerator\ItemEntityStorageInterface $item_entity_storage */
    $item_entity_storage = $this->entityTypeManager()->getStorage('item_entity');
    $langcode = $item_entity->language()->getId();
    $langname = $item_entity->language()->getName();
    $languages = $item_entity->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $item_entity->label()]) : $this->t('Revisions for %title', ['%title' => $item_entity->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all item revisions") || $account->hasPermission('administer item entities')));
    $delete_permission = (($account->hasPermission("delete all item revisions") || $account->hasPermission('administer item entities')));
    $rows = [];
    $vids = $item_entity_storage->revisionIds($item_entity);
    $latest_revision = TRUE;
    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\og_accelerator\ItemEntityInterface $revision */
      $revision = $item_entity_storage->loadRevision($vid);
      /** Only show revisions that are affected by the language that is being displayed. */
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];
        /** Use revision link to link to revisions that are not active. */
        $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $item_entity->getRevisionId()) {
          $link = Link::fromTextAndUrl($date, new Url('entity.item_entity.revision', [
            'item_entity' => $item_entity->id(),
            'item_entity_revision' => $vid,
          ]));
        } else {
          $link = $item_entity->toLink($date);
        }
        $row = [];
        $column = [
          'data' => [
            '#type' => 'inline_template',
            '#template' => '{{ date }} {% trans %}by {{ username }}{% endtrans %}{% if message %}<p class="revision-log">{{ message }}</p>{% endif %}',
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
        } else {
          $links = [];
          if ($revert_permission) {
            $links['revert'] = [
              'title' => $this->t('Revert'),
              'url' => $has_translations ?
                Url::fromRoute('entity.item_entity.translation_revert', [
                  'item_entity' => $item_entity->id(),
                  'item_entity_revision' => $vid,
                  'langcode' => $langcode,
                ]) :
                Url::fromRoute('entity.item_entity.revision_revert', [
                  'item_entity' => $item_entity->id(),
                  'item_entity_revision' => $vid,
                ]),
            ];
          }
          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.item_entity.revision_delete', [
                'item_entity' => $item_entity->id(),
                'item_entity_revision' => $vid,
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
    $build['item_entity_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];
    return $build;
  }
}
