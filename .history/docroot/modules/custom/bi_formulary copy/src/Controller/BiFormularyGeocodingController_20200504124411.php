<?php

namespace Drupal\bi_formulary\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\bi_formulary\Ajax\BiFormularyReverseGeocoding;
use Drupal\bi_formulary\BiFormularyGeocodingService;

/**
 * Class BiFormularyGeocodingController.
 */
class BiFormularyGeocodingController extends ControllerBase {

  /**
   * Drupal\bi_formulary\BiFormularyGeocodingService definition.
   *
   * @var \Drupal\bi_formulary\BiFormularyGeocodingService
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
      $container->get('bi_formulary.geocoding')
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
