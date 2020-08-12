<?php

namespace Drupal\bi_formulary\Ajax;

use Drupal\Core\Ajax\CommandInterface;

/**
 * Class ReverseGeocodingAjaxCommand.
 */
class BiFormularyReverseGeocoding implements CommandInterface {

  /**
   * The Users geolocation information.
   *
   * @var array
   */
  protected $geolocation;

  /**
   * Returns the user's reverse geocoded info.
   *
   * @param array $responseData
   *   User longitude.
   */
  public function __construct(array $responseData) {
    $this->geolocation = $responseData;
  }

  /**
   * Render custom ajax command.
   *
   * @return ajax
   *   Command function.
   */
  public function render() {
    return [
      'command' => 'get',
      'data' => $this->geolocation,
    ];
  }

}
