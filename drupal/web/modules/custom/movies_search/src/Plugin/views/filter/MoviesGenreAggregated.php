<?php

namespace Drupal\movies_search\Plugin\views\filter;

use Drupal\taxonomy\Entity\Term;
use Drupal\views\Plugin\views\display\DisplayPluginBase;
use Drupal\views\Plugin\views\filter\InOperator;
use Drupal\views\ViewExecutable;
use Elasticsearch\Client;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @ViewsFilter("movies_genre_aggregated")
 */
class MoviesGenreAggregated extends InOperator {

  /** @var \Elasticsearch\Client $elasticsearchClient */
  protected $elasticsearchClient;

  /** @var null|array $aggregations */
  protected $aggregations = NULL;

  /**
   * MoviesGenreAggregated constructor.
   *
   * @param array $configuration
   * @param string $plugin_id
   * @param mixed $plugin_definition
   * @param \Drupal\movies_search\Plugin\views\filter\Client $elasticsearch_client
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, Client $elasticsearch_client) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->elasticsearchClient = $elasticsearch_client;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('elasticsearch_helper.elasticsearch_client')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function init(ViewExecutable $view, DisplayPluginBase $display, array &$options = NULL) {
    parent::init($view, $display, $options);

    $this->definition['options callback'] = [$this, 'getGenreFilterOptions'];
  }

  /**
   * Returns rating aggregations.
   *
   * @return array
   */
  public function getAggregations() {
    if (!is_array($this->aggregations)) {
      /** @var \Drupal\movies_search\Plugin\ElasticsearchQueryBuilder\MovieSearch $query_builder */
      $query_builder = $this->view->getQuery()->getQueryBuilder();

      $query = [
        'index' => $query_builder->getIndices(),
        'size' => 0,
        'body' => [
          'aggs' => [
            'genres' => [
              'terms' => [
                'field' => 'field_movie_genres.target_id',
              ],
            ],
          ],
        ],
      ];

      // Execute the query.
      $result = $this->elasticsearchClient->search($query);

      // Build the response.
      $this->aggregations = [
        'genres' => $result['aggregations']['genres']['buckets'],
      ];
    }

    return $this->aggregations;
  }

  /**
   * Return aggregated genre names.
   *
   * @return mixed
   */
  public function getGenreFilterOptions() {
    $aggregations = $this->getAggregations();

    $tids = array_map(function($item) {
      return $item['key'];
    }, $aggregations['genres']);

    return array_combine($tids, array_map(function($tid) {
      return Term::load($tid)->label();
    }, $tids));
  }

}
