<?php

namespace Drupal\acme_sports_nfl_teams;

/**
 * Defines methods for AcmeSportsNflTeamsApiService.
 */
interface AcmeSportsNflTeamsApiServiceInterface {

  /**
   * Get all U.S states, used for populating select lists.
   *
   * @return array
   *   Returns an array of all U.S states with I.D's.
   */
  public function getStates();

  /**
   * Get all U.S states, used for populating select lists.
   *
   * @return array
   *   Returns an array of all drugs/products.
   */
  public function getDrugs();

  /**
   * Authenticates data to a Drupal site.
   *
   * @param int $state_id
   *   The I.D retrieved from getStates() method used
   *   as a parameter for this method.
   *
   * @return array
   *   Returns an array of all the health plans
   *   for a particuler state.
   */
  public function getHealthPlans($state_id);

  /**
   * Authenticates data to a Drupal site.
   *
   * @param int $drug_id
   *   The I.D retrieved from getStates() method used
   *   as a parameter for this method.
   * @param int $health_plan_id
   *   The I.D retrieved from getHealthPlans() method used
   *   as a parameter for this method.
   *
   * @return array
   *   Returns all the formulary information, such
   *   as co-pay, tier, additional information etc.
   */
  public function getDrug($drug_id, $health_plan_id);

}
