<?php

namespace Drupal\acme_sports_nfl_teams\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class BiFormularyConfigForm.
 */
class BiFormularyConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'acme_sports_nfl_teams.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'acme_sports_nfl_teams_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('acme_sports_nfl_teams.settings');

    $form['key'] = [
      '#type' => 'key_select',
      '#title' => $this->t('Formulary API credentials'),
      '#key_description' => TRUE,
      '#default_value' => $config->get('key') ? $config->get('key') : 'formulary_api_key',
    ];

    $form['key_geocoding'] = [
      '#type' => 'key_select',
      '#title' => $this->t('Google Geocoding services credentials'),
      '#key_description' => TRUE,
      '#default_value' => $config->get('key_geocoding') ? $config->get('key_geocoding') : 'formulary_geocoding_api_key',
    ];

    $form['state_coverage'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('State coverage'),
      '#description' => $this->t('Option to display percentage coverage by State based on the end-users chosen State.'),
      '#default_value' => $config->get('state_coverage'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    // Retrieve the configuration and set the values.
    $this->configFactory->getEditable('acme_sports_nfl_teams.settings')
      ->set('key', $form_state->getValue('key'))
      ->set('key_geocoding', $form_state->getValue('key_geocoding'))
      ->set('state_coverage', $form_state->getValue('state_coverage'))
      ->save();
  }

}
