<?php

/**
 * @file
 * Module theme preprocessors.
 */

use Drupal\acme_sports_nfl_teams\Form\BiFormularyForm;

/**
 * Implements hook_preprocess().
 */
function acme_sports_nfl_teams_preprocess_acme_sports_nfl_teams_results(&$variables) {
  // Hook for results template.
}

/**
 * Implements hook_preprocess().
 */
function acme_sports_nfl_teams_preprocess_form__acme_sports_nfl_teams_form(&$variables) {
  $test = $variables;
  $variables['no_results'] = 'TEST';
  $variables['download_link'] = 'TEST';
  $variables['#form'] = Drupal::formBuilder()->getForm(BiFormularyForm::class);
  $variables['form'] = Drupal::formBuilder()->getForm(BiFormularyForm::class);
}
