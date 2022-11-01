<?php
/**
 * @file
 * Contains landing page functionality for Lightning.
 */

use Drupal\views\ViewExecutable;
use \Drupal\views\Plugin\views\query\QueryPluginBase;

function ucr_article_views_query_alter(ViewExecutable $view, QueryPluginBase $query) {
    if ($view->id() == 'article_content_blocks') {
        $limit = $view->args[(count($view->args) - 1)];
        $query->setLimit($limit);
    }
}

function ucr_article_views_query_substitutions(ViewExecutable $view) {
    if ($view->id() == 'article_content_blocks') {
        $limit = $view->args[(count($view->args) - 1)];
        $view->query->setLimit($limit);
    }
}