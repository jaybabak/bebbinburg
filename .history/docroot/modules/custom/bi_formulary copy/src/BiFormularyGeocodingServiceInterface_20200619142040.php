<?php

namespace Drupal\acme_sports_nfl_teams;

/**
 * Defines methods for BiFormularyGeocodingService.
 */
interface BiFormularyGeocodingServiceInterface {

  /**
   * Return information about user's location.
   *
   * @param float $lat
   *   The user's latitude geo-coordinates.
   * @param float $long
   *   The user's longitude geo-coordinates.
   *
   * @return array
   *   Returns user's State (full and short name),
   *   and country code.
   */
  public function getUserLocation(float $lat, float $long);

}
