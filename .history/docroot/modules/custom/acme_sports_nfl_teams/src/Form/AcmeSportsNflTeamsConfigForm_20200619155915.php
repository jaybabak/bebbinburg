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

    $form['base_url'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Base URL'),
      '#description' => $this->t('Option to display percentage coverage by State based on the end-users chosen State.'),
      '#default_value' => $config->get('base_url') ? $config->get('base_url') : 'http://delivery.chalk247.com/team_list/NFL.JSON',
    ];

    $form['base_url'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Base URL'),
      '#description' => $this->t('Option to display percentage coverage by State based on the end-users chosen State.'),
      '#default_value' => $config->get('base_url') ? $config->get('base_url') : 'http://delivery.chalk247.com/team_list/NFL.JSON',
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
      ->set('base_url', $form_state->getValue('base_url'))
      ->save();
  }

}
