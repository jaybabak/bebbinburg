<?php

namespace Drupal\bi_formulary\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\bi_formulary\BiFormularyApiService;

/**
 * Provides a 'FormularySearchBlock' block.
 *
 * @Block(
 *  id = "formulary_search_block",
 *  admin_label = @Translation("Formulary Search Block"),
 * )
 */
class FormularySearchBlock extends BlockBase implements ContainerFactoryPluginInterface {

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
   * @var \Drupal\bi_formulary\BiFormularyApiService
   */
  protected $ftf;

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
   * @param \Drupal\bi_formulary\BiFormularyApiService $ftf
   *   The formulary api service.
   */
  public function __construct(array $configuration, $plugin_id, array $plugin_definition, ConfigFactoryInterface $config_factory, FormBuilderInterface $formBuilder, BiFormularyApiService $ftf) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->configFactory = $config_factory;
    $this->formBuilder = $formBuilder;
    $this->ftf = $ftf;
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
      $container->get('bi_formulary.api')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    // Define all the fields for block config modal.
    $form['product_name'] = [
      '#type' => 'select',
      '#title' => $this->t('Drug'),
      '#options' => $this->ftf->getDrugsDropdownOptions(),
      '#required' => TRUE,
      '#default_value' => $this->configuration['product_name'],
    ];

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

    // PDF button toggle.
    $form['pdf_download'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show download button'),
      '#default_value' => $this->configuration['pdf_download'],
      '#description' => $this->t('Displays the PDF download button.'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockValidate($form, FormStateInterface $form_state) {
    // Validat drugs/product drop down.
    if ($form_state->getValue('product_name') == "0") {
      $form_state->setErrorByName('product_name', $this->t('Cannot leave this field blank.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['pdf_download'] = $form_state->getValue('pdf_download');
    $this->configuration['product_name'] = $form_state->getValue('product_name');
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
    // Get and render the form.
    $form = $this->formBuilder->getForm('Drupal\bi_formulary\Form\BiFormularyForm', $parameters);
    // Attach the formulary library.
    $form['#attached']['library'] = ['bi_formulary/formulary'];
    // Return the form.
    return $form;
  }

}
