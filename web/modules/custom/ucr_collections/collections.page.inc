<?php

/**
 * @file
 * Contains collections.page.inc.
 *
 * Page callback for Collection Item entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Collection Item templates.
 *
 * Default template: collections.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_collections(array &$variables) {
  // Fetch CollectionsEntity Entity Object.
  $collections = $variables['elements']['#collections'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
