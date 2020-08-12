<?php

namespace Drupal\acme_sports_nfl_teams\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\acme_sports_nfl_teams\AcmeSportsNflTeamsApiService;

/**
 * Provides a 'AcmeSportsNflTeamsBlock' block.
 *
 * @Block(
 *  id = "acme_sports_nfl_teams_block",
 *  admin_label = @Translation("Acme Sports NFL Teams Block"),
 * )
 */
class AcmeSportsNflTeamsBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The config factory service.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Config factory service.
   *
   * @var \Drupal\acme_sports_nfl_teams\AcmeSportsNflTeamsApiService
   */
  protected $nfl;

  /**
   * Creates a new instance of FormularyBlock.
   *
   * @param array $configuration
   *   The block configuration.
   * @param string $plugin_id
   *   The plugin ID.
   * @param array $plugin_definition
   *   The plugin definition.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory service.
   * @param \Drupal\acme_sports_nfl_teams\AcmeSportsNflTeamsApiService $nfl
   *   The formulary api service.
   */
  public function __construct(array $configuration, $plugin_id, array $plugin_definition, ConfigFactoryInterface $config_factory, AcmeSportsNflTeamsApiService $nfl) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->configFactory = $config_factory;
    $this->nfl = $nfl;
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory'),
      $container->get('acme_sports_nfl_teams.api')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {

    // Disclaimer text.
    $form['disclaimer'] = [
      '#type' => 'details',
      '#title' => $this->t('Disclaimer'),
      '#collapsible' => TRUE,
    ];

    $form['disclaimer']['disclaimer_message'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Disclaimer text e.g: "We are not responsible for data provided by any external API."'),
      '#default_value' => isset($this->configuration['disclaimer_message']) ? $this->configuration['disclaimer_message'] : '',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockValidate($form, FormStateInterface $form_state) {
    // Void. Left blank.
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['disclaimer_message'] = $form_state->getValue(['disclaimer', 'disclaimer_message']);
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    // Attach the acme sports nfl teams library.
    $form['#attached']['library'] = ['acme_sports_nfl_teams/library'];
    // TWIG Template to use.
    $form['#theme'] = 'nfl_teams_results';
    // Defaults values.
    $form['#data'] = NULL;
    $form['#no_results'] = NULL;
    $form['#error'] = NULL;

    // Pass in a block configuration settings.
    $parameters = $this->configuration;

    // Get the data from NFL teams service.
    $data = $this->nfl->getSportsTeams();

    if (!isset($data["results"])) {
      $form['#no_results'] = $this->t('Sorry no results to display');
    }

    $form['#data'] = 'Download link';

    $form['#error'] = 'Error';

    // Return the form.
    return $form;
  }

}
