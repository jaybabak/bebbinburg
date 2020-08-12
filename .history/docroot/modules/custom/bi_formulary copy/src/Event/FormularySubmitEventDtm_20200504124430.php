<?php

namespace Drupal\bi_formulary\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class FormularySubmitEventDtm.
 *
 * @package Drupal\bi_formulary\Event
 */
class FormularySubmitEventDtm extends Event {

  const EVENT_NAME = 'bi_formulary.submit_event_dtm';

  /**
   * The form array.
   *
   * @var array
   */
  protected $form;

  /**
   * The deviceId or type e.g desktop, mobile etc...
   *
   * @var string
   */
  protected $type;

  /**
   * The information about device making request.
   *
   * @var array
   */
  protected $info;

  /**
   * The drug ID.
   *
   * @var string
   */
  protected $drugId;

  /**
   * The drug name.
   *
   * @var string
   */
  protected $drugName;

  /**
   * The health plan ID.
   *
   * @var string
   */
  protected $healthPlanId;

  /**
   * The health plan name.
   *
   * @var string
   */
  protected $healthPlanName;

  /**
   * The health plan name.
   *
   * @var string
   */
  protected $stateId;

  /**
   * The state name.
   *
   * @var string
   */
  protected $stateName;

  /**
   * FormularySubmitEventDtm constructor.
   *
   * @param array $form
   *   The form submission values from FTF search.
   */
  public function __construct(array $form) {
    $this->form = $form;
    $this->type = $form['type'];
    $this->info = $form['info'];
    $this->drugId = $form['drug_id'];
    $this->drugName = $form['drug_name'];
    $this->healthPlanId = $form['health_plan_id'];
    $this->healthPlanName = $form['health_plan_name'];
    $this->stateId = $form['state_id'];
    $this->stateName = $form['state_name'];
  }

  /**
   * Get all information about FTF search.
   *
   * @return array
   *   The adverse word that was detected.
   */
  public function getFormValues() {
    // Return the form values submitted by client.
    $form = [
      'state' => $this->stateName,
      'drug_name' => $this->drugName,
      'health_plan_name' => $this->healthPlanName,
      'device' => $this->type,
    ];

    return $form;
  }

  /**
   * Get the device type.
   *
   * @return string
   *   The device type: desktop, mobile etc...
   */
  public function getDeviceType() {
    return $this->type;
  }

  /**
   * Get all information about FTF search.
   *
   * @return array
   *   Information such as OS, browser etc...
   */
  public function getAdditionalDeviceInfo() {
    return $this->info;
  }

  /**
   * Get the drug Id.
   *
   * @return string
   *   The adverse word that was detected.
   */
  public function getDrugId() {
    return $this->drugId;
  }

  /**
   * Get the drug name.
   *
   * @return string
   *   The selected drug name.
   */
  public function getDrugName() {
    return $this->drugName;
  }

  /**
   * Get the health plan id.
   *
   * @return string
   *   The selected health plan id.
   */
  public function getHealthPlanId() {
    return $this->healthPlanId;
  }

  /**
   * Get the health plan name.
   *
   * @return string
   *   The selected health plan name.
   */
  public function getHealthPlanName() {
    return $this->healthPlanName;
  }

  /**
   * Get the state id.
   *
   * @return string
   *   The selected state id.
   */
  public function getStateId() {
    return $this->stateId;
  }

  /**
   * Get the state name.
   *
   * @return string
   *   The selected state name.
   */
  public function getStateName() {
    return $this->stateName;
  }

}
