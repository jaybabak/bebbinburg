<?php

namespace Drupal\acme_sports_nfl_teams\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class AcmeSportsNflTeamsConfigForm.
 */
class AcmeSportsNflTeamsConfigForm extends ConfigFormBase {

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

    // Holds the base url for NFL api.
    $form['base_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Base URL'),
      '#description' => $this->t('The base URL for the API (NFL Teams).'),
      '#default_value' => $config->get('base_url'),
    ];

    // Holds the API key credentials for NFL api.
    $form['api_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('API Key'),
      '#description' => $this->t('API key for retrieving data from endpoints.'),
      '#default_value' => $config->get('api_key'),
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
      ->set('base_url', $form_state->getValue('base_url'))
      ->set('api_key', $form_state->getValue('api_key'))
      ->save();
  }

}
