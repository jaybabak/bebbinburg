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
   * The base URL for NFL api service.
   *
   * @var string
   */
  protected $baseUrl;

  /**
   * The API key.
   *
   * @var string
   */
  protected $apiKey;

  /**
   * Constructs a new AcmeSportsNflTeamsApiService object.
   */
  public function __construct(ConfigFactoryInterface $configFactory, Client $httpClient) {
    $this->config = $configFactory->get('acme_sports_nfl_teams.settings');
    $this->client = $httpClient;
    $this->baseUrl = $this->config->get('base_url');
    $this->apiKey = $this->config->get('api_key');
  }

  /**
   * Function to retrieve the NFL sports teams data.
   *
   * @return array
   *   Return the array of sports teams information.
   */
  public function getSportsTeams() {
    // Normally methods like this (getSportsTeams) would go into a PHP
    // library/composer package and wrapped around a service class like this.
    // To keep things concise and simple it was added here instead.
    try {
      // Make request to geocoding API.
      $response = $this->client->request('GET', $this->baseUrl, [
        'query' => [
          'api_key' => $this->apiKey,
        ],
      ]);

      // Take the JSON object and convert to an array.
      $contents = (string) $response->getBody();
      $contents = json_decode($contents, TRUE);

      // Return the array.
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
