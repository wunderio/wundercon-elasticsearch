<?php

namespace Drupal\movies_search\Plugin\ElasticsearchQueryBuilder;

use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\elasticsearch_helper_views\Plugin\ElasticsearchQueryBuilder\ElasticsearchQueryBuilderPluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @ElasticsearchQueryBuilder(
 *   id = "movies_search",
 *   label = @Translation("Movies search"),
 *   description = @Translation("Provides search for movies.")
 * )
 */
class MovieSearch extends ElasticsearchQueryBuilderPluginBase {

  /** @var \Drupal\Core\Language\LanguageInterface $currentLanguage */
  protected $currentLanguage;

  /**
   * MovieSearch constructor.
   *
   * @param array $configuration
   * @param string $plugin_id
   * @param mixed $plugin_definition
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, LanguageManagerInterface $language_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->currentLanguage = $language_manager->getCurrentLanguage();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('language_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildQuery() {
    // Get filter values.
    $values = $this->getFilterValues();

    $query = [
      'index' => $this->getIndices(),
      'body' => [
      ],
    ];

    if (!empty($values['genre_aggregated'])) {
      $query['body']['query']['bool']['must'][]['terms']['field_movie_genres.target_id'] = $values['genre_aggregated'];
    }

    if (!empty($values['genre_static'])) {
      $query['body']['query']['bool']['must'][]['terms']['field_movie_genres.target_id'] = $values['genre_static'];
    }

    if (!empty($values['keyword'])) {
      $query['body']['query']['bool']['must'][]['match']['content'] = $values['keyword'];
    }

    return $query;
  }

  /**
   * Returns a list of indices that search should be performed on.
   *
   * @return array
   */
  public function getIndices() {
    $langcode = $this->currentLanguage->getId();

    return [
      'content-node-' . $langcode,
    ];
  }

}
