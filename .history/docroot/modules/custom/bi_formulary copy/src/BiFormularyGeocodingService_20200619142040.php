<?php

namespace Drupal\acme_sports_nfl_teams;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\key\KeyRepositoryInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

/**
 * Class BiFormularyGeocodingService.
 */
class BiFormularyGeocodingService {

  /**
   * Config factory service.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $config;

  /**
   * Key Service.
   *
   * @var \Drupal\key\KeyRepositoryInterface
   */
  protected $keyRepository;

  /**
   * HTTP Services - Contents HTTP Client.
   *
   * @var \GuzzleHttp\Client
   */
  protected $client;

  /**
   * The API key.
   *
   * @var string
   */
  protected $apiKey;

  /**
   * The base URL for geocoding service.
   *
   * @var string
   */
  protected $baseUrl;

  /**
   * Constructs a new BiFormularyGeocodingService object.
   */
  public function __construct(ConfigFactoryInterface $configFactory, KeyRepositoryInterface $keyRepository, Client $httpClient) {
    $this->config = $configFactory->get('acme_sports_nfl_teams.settings');
    $this->keyRepository = $keyRepository;
    $this->client = $httpClient;
    // Api credentials.
    $api_credentials = $this->getCredentials();
    $this->baseUrl = $api_credentials['base_url'];
    $this->apiKey = $api_credentials['key'];
  }

  /**
   * Method for doing reverseGeocode user lat/lon.
   *
   * Converts using google geocoding service.
   *
   * @param float $lat
   *   User latitude.
   * @param float $long
   *   User longitude.
   */
  public function getUserLocation(float $lat, float $long) {

    try {
      // Make request to geocoding API.
      $response = $this->client->request('GET', $this->baseUrl, [
        'query' => [
          'latlng' => $lat . ',' . $long,
          'key' => $this->apiKey,
        ],
      ]);

      $contents = (string) $response->getBody();
      $contents = json_decode($contents, TRUE);

      // Do not need all the information only state and country.
      $address = [
        'state' => $contents['results'][0]['address_components'][5]['short_name'],
        'state_full' => $contents['results'][0]['address_components'][5]['long_name'],
        'country' => $contents['results'][0]['address_components'][6]['long_name'],
      ];

      return $address;
    }
    catch (RequestException $e) {
      $error = [
        'message' => $e->getMessage(),
      ];
      // If error return response.
      return $error;
    }

  }

  /**
   * Retrieve the credentials for the formulary API service.
   */
  protected function getCredentials() {
    $credentials = [];
    $keyIsSet = $this->config->get('key_geocoding');
    if (!$keyIsSet) {
      // If overriden config in Platform doesn't have a proper value.
      $keyIsSet = $this->keyRepository->getKey('formulary_geocoding_api_key')->id();
    }
    if ($keyIsSet) {
      $key = $this->keyRepository->getKey($keyIsSet);
      if ($key) {
        $credentials = $key->getKeyValues();
      }
    }
    return $credentials;
  }

}
