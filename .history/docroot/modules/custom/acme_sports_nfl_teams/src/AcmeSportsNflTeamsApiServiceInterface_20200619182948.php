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
   *   Returns an array of sports teams from the NFL Api.
   */
  public function getSportsTeams();

}
