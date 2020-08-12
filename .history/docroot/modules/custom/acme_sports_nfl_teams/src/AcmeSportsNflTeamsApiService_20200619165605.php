<?php

namespace Drupal\acme_sports_nfl_teams;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

/**
 * Class AcmeSportsNflTeamsApiService.
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
    $this->baseUrl = $this->config->data["acme_sports_nfl_teams"]["base_url"];
    $this->apiKey = $this->config->data["acme_sports_nfl_teams"]["api_key"];
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

      // Return the data array.
      return $contents;
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
