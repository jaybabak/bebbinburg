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
   * Constructs a new AcmeSportsNflTeamsApiService object.
   */
  public function __construct(ConfigFactoryInterface $configFactory, Client $httpClient) {
    $this->config = $configFactory->get('acme_sports_nfl_teams.settings');
    $this->client = $httpClient;
  }

  /**
   * Helper function to retrieve the NFL sports teams.
   *
   * @return array
   *   Gets sports teams information.
   */
  public function getSportsTeams() {
    // Get all states.
    $states = $this->getStates();
    // Set initial value.
    $state_coverage = 0;
    // Find the current state coverage information.
    foreach ($states as $state) {
      if ((int) $state_id == $state["id"]) {
        $state_coverage = $state["coverage"];
      }
    }

    return $state_coverage;
  }

}
