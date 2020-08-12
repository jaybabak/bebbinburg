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
  public function getSportsTeams();

}
