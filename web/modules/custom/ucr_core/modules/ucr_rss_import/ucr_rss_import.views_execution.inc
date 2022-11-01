<?php
/**
 * @file
 * Contains landing page functionality for Lightning.
 */

use Drupal\views\ViewExecutable;
use \Drupal\views\Plugin\views\query\QueryPluginBase;

function ucr_rss_import_views_query_alter(ViewExecutable $view, QueryPluginBase $query) {
    if (($view->id() == 'feeds_import_display') && ($view->getDisplay()->display['id'] == "feeds_import_display_block")) {
        $limit = $view->args[1];
        if(!empty($limit)) {
            $query->setLimit($limit);
        }
    }
}

function ucr_rss_import_views_query_substitutions(ViewExecutable $view) {
    if (($view->id() == 'feeds_import_display') && ($view->getDisplay()->display['id'] == "feeds_import_display_block")) {
        $limit = $view->args[1];
        if(!empty($limit)) {
            $view->query->setLimit($limit);
        }
    }
}