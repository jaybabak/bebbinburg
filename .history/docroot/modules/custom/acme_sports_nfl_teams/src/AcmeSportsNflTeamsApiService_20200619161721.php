<?php

namespace Drupal\acme_sports_nfl_teams;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

/**
 * Class BiFormularyApiService.
 */
class AcmeSportsNflTeamsApiService {

  use StringTranslationTrait;

  /**
   * Config factory service.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $config;

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
   * Constructs a new AcmeSportsNflTeamsApiService object.
   */
  public function __construct(ConfigFactoryInterface $configFactory, Client $httpClient) {
    $this->config = $configFactory->get('acme_sports_nfl_teams.settings');
    $this->client = $httpClient;
    $this->baseUrl = $this->config->get('base_url');
    $this->apiKey = $this->config->get('api_key');;
  }

  /**
   * Helper function to retrieve the NFL sports teams.
   *
   * @return array
   *   Gets sports teams information.
   */
  public function getSportsTeams() {
    try {
      // Make request to geocoding API.
      $response = $this->client->request('GET', $this->baseUrl, [
        'query' => [
          'api_key' => $this->apiKey,
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

}
