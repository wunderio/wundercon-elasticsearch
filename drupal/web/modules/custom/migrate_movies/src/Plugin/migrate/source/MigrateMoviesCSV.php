<?php

namespace Drupal\migrate_movies\Plugin\migrate\source;

use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\Row;
use Drupal\migrate_source_csv\Plugin\migrate\source\CSV as MigrateSourceCSV;

/**
 * Movies base migration.
 *
 * @MigrateSource(
 *   id = "migrate_movies_csv"
 * )
 */
class MigrateMoviesCSV extends MigrateSourceCSV {

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration) {
    $module_path = drupal_get_path('module', 'migrate_movies');

    $configuration['path'] = $module_path . '/source/' . $configuration['path'];

    parent::__construct($configuration, $plugin_id, $plugin_definition, $migration);
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    $keywords = json_decode($row->getSourceProperty('keywords'));

    // Proceed if keywords can be extracted.
    if (is_array($keywords)) {
      $keywords = array_map(function($item) {
        return $item->name;
      }, $keywords);
    }
    // Set keywords to an empty array if decoding fails.
    else {
      $keywords = [];
    }

    $row->setSourceProperty('keywords', $keywords);
    return parent::prepareRow($row);
  }

}
