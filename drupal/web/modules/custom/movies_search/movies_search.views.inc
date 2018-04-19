<?php

/**
 * Implements hook_views_data();
 */
function movies_search_views_data() {
  $data['elasticsearch_result']['keyword'] = [
    'title' => t('Keyword'),
    'filter' => [
      'title' => t('Keyword'),
      'id' => 'movies_keyword',
    ],
  ];

  $data['elasticsearch_result']['genre_aggregated'] = [
    'title' => t('Genre (aggregated)'),
    'filter' => [
      'title' => t('Genre (aggregated)'),
      'id' => 'movies_genre_aggregated',
    ],
  ];

  $data['elasticsearch_result']['genre_static'] = [
    'title' => t('Genre (static)'),
    'filter' => [
      'title' => t('Genre (static)'),
      'id' => 'movies_genre_static',
    ],
  ];

  return $data;
}
