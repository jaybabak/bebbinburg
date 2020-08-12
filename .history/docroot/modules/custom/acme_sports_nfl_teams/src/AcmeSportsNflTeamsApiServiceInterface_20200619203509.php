<?php

namespace Drupal\acme_sports_nfl_teams;

/**
 * Defines methods for AcmeSportsNflTeamsApiService.
 */
interface AcmeSportsNflTeamsApiServiceInterface {

  /**
   * Get informationg relating to football teams.
   *
   * @return array
   *   Returns an array of sports teams from the NFL Api. Data might
   *   include name, display name etc...
   */
  public function getSportsTeams();

}
