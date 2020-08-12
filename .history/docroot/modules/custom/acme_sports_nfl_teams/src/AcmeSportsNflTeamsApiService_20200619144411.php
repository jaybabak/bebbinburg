<?php

namespace Drupal\acme_sports_nfl_teams;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\key\KeyRepositoryInterface;
use FingertipFormulary\Config;
use FingertipFormulary\Client\Client;
use FingertipFormulary\FingertipFormulary;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

/**
 * Class BeFormularyApiService.
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
   * Key Service.
   *
   * @var \Drupal\key\KeyRepositoryInterface
   */
  protected $keyRepository;

  /**
   * FingertipFormulary Client service.
   *
   * @var \FingertipFormulary\Client\Client
   */
  protected $client;

  /**
   * FingertipFormulary Config.
   *
   * @var \FingertipFormulary\Config
   */
  protected $ftfConfig;

  /**
   * Config Factory service.
   *
   * @var \FingertipFormulary\FingertipFormulary
   */
  protected $ftf;

  /**
   * HTTP Services - Contents HTTP Client.
   *
   * @var \GuzzleHttp\Client
   */
  protected $client;

  /**
   * Constructs a new AcmeSportsNflTeamsApiService object.
   */
  public function __construct(ConfigFactoryInterface $configFactory, KeyRepositoryInterface $keyRepository, Client $httpClient) {
    $this->config = $configFactory->get('acme_sports_nfl_teams.settings');
    $this->keyRepository = $keyRepository;
    // Api credentials.
    $api_credentials = $this->getCredentials();
    // Initalize the FTF libary.
    $this->ftfConfig = new Config($api_credentials['key'], $api_credentials['base_url']);
    $this->client = new Client([], $this->ftfConfig);
    $this->ftf = new FingertipFormulary($this->client);
    $this->client = $httpClient;
  }

  /**
   * A method for retrieving all states.
   */
  public function getStates() {

    $states = $this->ftf->api()->states()->all();

    $all_states = [];

    foreach ($states as &$state) {
      $all_states[] = [
        'id' => $state->id(),
        'name' => $state->name(),
        'abbreviation' => $state->abbreviation(),
        'href' => $state->href(),
        'coverage' => $state->coverage(),
      ];
    }
    return $all_states;
  }

  /**
   * A method for getting all products/drugs.
   */
  public function getDrugs() {

    $drugs = $this->ftf->api()->drugs()->all();

    $all_drugs = [];

    foreach ($drugs as &$drug) {
      $all_drugs[] = [
        'id' => $drug->id(),
        'name' => $drug->name(),
        'displayId' => $drug->displayId(),
        'href' => $drug->href(),
      ];
    }
    return $all_drugs;
  }

  /**
   * A method for getting all health plans by state id.
   */
  public function getHealthPlans($state_id) {

    $health_plans = $this->ftf->api()->healthPlans($state_id)->all();

    $all_health_plans = [];

    foreach ($health_plans as &$health_plan) {
      $all_health_plans[] = [
        'health_plan' => $health_plan['health_plan'],
      ];
    }
    return $all_health_plans;
  }

  /**
   * A method for getting formulary information.
   *
   * Takes drug id and health plan id as params.
   */
  public function getDrug($drug_id, $health_plan_id) {

    $formulary_info = $this->ftf->api()->formularies($drug_id, $health_plan_id)->all();

    $formulary_data = [];

    foreach ($formulary_info as &$formulary) {
      $formulary_data[] =
      [
        'health_plan_id' => $formulary->healthPlanId(),
        'health_plan_name' => $formulary->healthPlanName(),
        'health_plan_url' => $formulary->href(),
        'drug_name' => $formulary->drugName(),
        'drug_id' => $formulary->drugId(),
        'reason_code' => $formulary->reasonCode(),
        'reason' => $formulary->reason(),
        'tier' => $formulary->tier(),
        'tier_name' => $formulary->tierName(),
        'tier_text' => $formulary->tierText(),
        'copays' => $formulary->copays(),
        'qualifier_details' => $formulary->qualifierDetails(),
      ];
    }
    return $formulary_data;
  }

  /**
   * Helper function to populate the states dropdown.
   *
   * @return array
   *   Returns <option> element for Form API
   */
  public function getStatesDropdownOptions() {
    // Get all states.
    $states = $this->getStates();
    // Set none option.
    $state_names[] = $this->t('- Select -');
    $state_ids[] = '0';
    // Create options array.
    foreach ($states as $state) {
      $state_names[] = $state['name'];
      $state_ids[] = $state['id'];
    }

    return array_combine($state_ids, $state_names);
  }

  /**
   * Helper function to populate the drugs dropdown.
   *
   * @return array
   *   Returns <option> element for Form API
   */
  public function getDrugsDropdownOptions() {
    // Get all drugs and format array for options.
    $drugs = $this->getDrugs();
    // Set none option.
    $drug_names[] = $this->t('- Select -');
    $drug_ids[] = '0';
    // Create options array.
    foreach ($drugs as $drug) {
      $drug_names[] = $drug['name'];
      $drug_ids[] = $drug['id'];
    }

    return array_combine($drug_ids, $drug_names);
  }

  /**
   * Helper function to populate the healthplan dropdown.
   *
   * @param string $key
   *   This will determine which set of options is returned.
   *
   * @return array
   *   Returns <option> element for Form API
   */
  public function getHealthPlansDropdownOptions($key = '') {
    if ($key == 'none') {
      $health_plan_names[] = $this->t('- Select -');
      $health_plan_ids[] = 'none';
      return array_combine($health_plan_ids, $health_plan_names);
    }

    $health_plans = $this->getHealthPlans($key);
    // Set none option.
    $health_plan_names[] = $this->t('- Select -');
    $health_plan_ids[] = 'none';
    // Create options array.
    foreach ($health_plans as $health_plan) {
      $health_plan_names[] = $health_plan['health_plan']['webname'];
      $health_plan_ids[] = $health_plan['health_plan']['provider']['id'];
    }

    return array_combine($health_plan_ids, $health_plan_names);
  }

  /**
   * Helper function to retrieve coverage stats.
   *
   * @param string $state_id
   *   This current selected state.
   *
   * @return array
   *   Gets coverage information.
   */
  public function getStateCoverageInformation($state_id) {
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

  /**
   * Retrieve the credentials for the formulary API service.
   */
  protected function getCredentials() {
    $credentials = [];
    $keyIsSet = $this->config->get('key');
    if (!$keyIsSet) {
      // If overriden config in Platform doesn't have a proper value.
      $keyIsSet = $this->keyRepository->getKey('formulary_api_key')->id();
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
