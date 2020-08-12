<?php

namespace Drupal\acme_sports_nfl_teams\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\acme_sports_nfl_teams\BiFormularyApiService;

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
   * The form builder.
   *
   * @var \Drupal\Core\Form\FormBuilderInterface
   */
  protected $formBuilder;

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
   * @param \Drupal\Core\Form\FormBuilderInterface $formBuilder
   *   The form builder service.
   * @param \Drupal\acme_sports_nfl_teams\AcmeSportsNflTeamsApiService $nfl
   *   The formulary api service.
   */
  public function __construct(array $configuration, $plugin_id, array $plugin_definition, ConfigFactoryInterface $config_factory, FormBuilderInterface $formBuilder, AcmeSportsNflTeamsApiService $nfl) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->configFactory = $config_factory;
    $this->formBuilder = $formBuilder;
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
      $container->get('form_builder'),
      $container->get('acme_sports_nfl_teams.api')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {

    // Heading fieldset/details.
    $form['headings'] = [
      '#type' => 'details',
      '#title' => $this->t('Headings'),
    ];

    $form['headings']['headline'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Headline'),
      '#default_value' => $this->configuration['headline'],
    ];

    $form['headings']['subheadline'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Subheadline'),
      '#default_value' => $this->configuration['subheadline'],
    ];

    $form['headings']['form_headline'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Form Headline'),
      '#default_value' => $this->configuration['form_headline'],
    ];

    // Disclaimer text.
    $form['disclaimer'] = [
      '#type' => 'details',
      '#title' => $this->t('Disclaimer'),
      '#collapsible' => TRUE,
    ];

    $form['disclaimer']['disclaimer_message'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Disclaimer text.'),
      '#default_value' => $this->configuration['disclaimer_message'],
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
    $this->configuration['headline'] = $form_state->getValue(['headings', 'headline']);
    $this->configuration['subheadline'] = $form_state->getValue(['headings', 'subheadline']);
    $this->configuration['form_headline'] = $form_state->getValue(['headings', 'form_headline']);
    $this->configuration['disclaimer_message'] = $form_state->getValue(['disclaimer', 'disclaimer_message']);
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    // Pass in a block configuration settings.
    $parameters = $this->configuration;
    // Attach the formulary library.
    $form['#attached']['library'] = ['acme_sports_nfl_teams/library'];
    $form['#markup'] = '<h1>Wohoooo!</h1>';
    // Return the form.
    return $form;
  }

}
