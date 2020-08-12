<?php

namespace Drupal\bi_carousel\Plugin\Layout;

use Drupal\Core\Layout\LayoutDefault;

/**
 * Provides a Teaser Grid layout class for housing teaser componenets.
 */
class TeaserGroupWithCarousel extends LayoutDefault {

  /**
   * {@inheritdoc}
   */
  public function build(array $regions) {
    // Creates the render array.
    $build = parent::build($regions);
    // Ensure that blocks are present in the "right" region.
    $content_right = isset($build["right"]);
    $content_left = isset($build["left"]);
    // To determine if custom classes should added or not depending on a route.
    $route_match = \Drupal::routeMatch()->getRouteName();
    // Determine if it's the layout builder page (admin/publisher).
    switch ($route_match) {
      case 'layout_builder.overrides.node.view':
      case 'layout_builder.add_block':
      case 'layout_builder.remove_block':
      case 'layout_builder.move_block':
      case 'layout_builder.update_block':
      case 'layout_builder.add_section':
        // Set $admin_route which is passed to TWIG template.
        $admin_route = TRUE;
        // Used to render correct classes for layout builder.
        $build['#settings']['is_admin'] = $admin_route;
        break;

      default:
        // Set $admin_route which is passed to TWIG template.
        $admin_route = FALSE;
        // Used to render correct classes for layout builder.
        $build['#settings']['is_admin'] = $admin_route;
        break;
    }

    // Add the right classes and fixes visual rendering issues with quick
    // links block due to GDS grid clashing with layout builder.
    // Also, add class if quicklinks block exists to fix height issue.
    if ($content_right) {
      foreach ($build["right"] as $key => $value) {
        $block_type = $value["content"]["#block_content"]->bundle();
        // Quick links block adding classes.
        if ($block_type == 'bi_quick_links') {
          // Fixes the heigt issue for bi quick_links in layout builder.
          $build['right'][$key]['#attributes']['class'][] = 'height-auto';
        }
      }
    }
    // Add class if quicklinks block exists to fix height issue.
    if ($content_left) {
      foreach ($build["left"] as $key => $value) {
        $block_type = $value["content"]["#block_content"]->bundle();
        // Quick links block adding classes.
        if ($block_type == 'bi_quick_links') {
          // Fixes the heigt issue for bi quick_links in layout builder.
          $build['left'][$key]['#attributes']['class'][] = 'height-auto';
        }
      }
    }
    // Return the modified render array.
    return $build;
  }

}
