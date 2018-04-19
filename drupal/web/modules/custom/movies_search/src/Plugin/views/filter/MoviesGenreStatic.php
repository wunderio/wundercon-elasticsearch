<?php

namespace Drupal\movies_search\Plugin\views\filter;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\taxonomy\Entity\Term;
use Drupal\views\Plugin\views\display\DisplayPluginBase;
use Drupal\views\Plugin\views\filter\InOperator;
use Drupal\views\ViewExecutable;
use Elasticsearch\Client;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @ViewsFilter("movies_genre_static")
 */
class MoviesGenreStatic extends InOperator {

  /** @var \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager */
  protected $entityTypeManager;

  /**
   * MoviesGenreStatic constructor.
   *
   * @param array $configuration
   * @param string $plugin_id
   * @param mixed $plugin_definition
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   * @param array $configuration
   * @param string $plugin_id
   * @param mixed $plugin_definition
   *
   * @return static
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')
    );
  }

  /**
   * @return array|null
   */
  public function getValueOptions() {
    $taxonomy_storage = $this->entityTypeManager->getStorage('taxonomy_term');
    $query = $taxonomy_storage->getQuery();
    $query->condition('vid', 'genres');
    $tids = $query->execute();

    if ($terms = $taxonomy_storage->loadMultiple($tids)) {
      $values = array_map(function($item) {
        return $item->label();
      }, $terms);
    }
    else {
      $values = [];
    }

    $this->valueOptions = $values;
  }

}
