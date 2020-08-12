<?php

/**
 * @file
 * Module theme preprocessors.
 */

use Drupal\bi_formulary\Form\BiFormularyForm;

/**
 * Implements hook_preprocess().
 */
function bi_formulary_preprocess_bi_formulary_results(&$variables) {
  // Hook for results template.
}

/**
 * Implements hook_preprocess().
 */
function bi_formulary_preprocess_form__bi_formulary_form(&$variables) {
  $test = $variables;
  $variables['no_results'] = 'TEST';
  $variables['download_link'] = 'TEST';
  $variables['#form'] = Drupal::formBuilder()->getForm(BiFormularyForm::class);
  $variables['form'] = Drupal::formBuilder()->getForm(BiFormularyForm::class);
}
