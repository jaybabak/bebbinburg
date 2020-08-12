<?php

namespace Drupal\acme_sports_nfl_teams\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\acme_sports_nfl_teams\Ajax\BiFormularyReverseGeocoding;
use Drupal\acme_sports_nfl_teams\BiFormularyGeocodingService;

/**
 * Class AcmeSportsNflTeamsontroller.
 */
class AcmeSportsNflTeamsController extends ControllerBase {

  /**
   * Drupal\acme_sports_nfl_teams\BiFormularyGeocodingService definition.
   *
   * @var \Drupal\acme_sports_nfl_teams\BiFormularyGeocodingService
   */
  protected $biFormularyGeocoding;

  /**
   * {@inheritdoc}
   */
  public function __construct(BiFormularyGeocodingService $geocoder) {
    $this->biFormularyGeocoding = $geocoder;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('acme_sports_nfl_teams.geocoding')
    );
  }

  /**
   * Hello.
   *
   * @return array
   *   Return Hello string.
   */
  public function reversegeocode(Request $request) {
    // Get all query params from POST request.
    $params = $request->get('query');
    // Create AJAX Response object.
    $response = new AjaxResponse();
    // If parameters (lat/long) are set.
    if (isset($params)) {
      // Make request to geocoding service.
      $content = $this->biFormularyGeocoding->getUserLocation($params['lat'], $params['long']);
      // Call the BiHcpUserAuthenticated javascript function.
      return $response->addCommand(new BiFormularyReverseGeocoding($content));
    }
    // Return ajax response.
    return $response;
  }

}
