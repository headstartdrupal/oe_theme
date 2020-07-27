<?php

/**
 * @file
 * Preprocessors for field_group_in_page_navigation elements.
 */

declare(strict_types = 1);

/**
 * Prepares variables for in-page navigation..
 *
 * Default template: oe-theme-helper-in-page-navigation.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - element: An associative array containing the properties and children of
 *     the in-page navigation element. Properties used: #children.
 */
function template_preprocess_field_group_in_page_navigation(array &$variables) {
  $element = $variables['element'];
  $variables['children'] = (!empty($element['#children'])) ? $element['#children'] : '';
}
