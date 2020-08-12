<?php

namespace Drupal\bi_formulary\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\bi_formulary\BiFormularyApiService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\bi_formulary\Event\FormularySubmitEventDtm;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Drupal\universal_device_detection\Detector\DefaultDetector;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Url;

/**
 * Class BiFormularyForm.
 */
class BiFormularyForm extends FormBase implements ContainerInjectionInterface {

  /**
   * Config factory service.
   *
   * @var \Drupal\bi_formulary\BiFormularyApiService
   */
  protected $ftf;

  /**
   * Drupal\Core\Render\RendererInterface definition.
   *
   * @var Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * Device Detector service.
   *
   * @var \Drupal\universal_device_detection\Detector\DefaultDetector
   */
  protected $deviceDetector;

  /**
   * Config factory service.
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $eventDispatcher;

  /**
   * Config factory service.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'bi_formulary_form';
  }

  /**
   * {@inheritdoc}
   */
  public function __construct(BiFormularyApiService $ftf, RendererInterface $renderer, DefaultDetector $deviceDetector, EventDispatcherInterface $eventDispatcher, ConfigFactoryInterface $configFactory) {
    $this->ftf = $ftf;
    $this->renderer = $renderer;
    $this->deviceDetector = $deviceDetector;
    $this->eventDispatcher = $eventDispatcher;
    $this->configFactory = $configFactory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('bi_formulary.api'),
      $container->get('renderer'),
      $container->get('universal_device_detection.default'),
      $container->get('event_dispatcher'),
      $container->get('config.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $parameter = NULL) {
    // Module configuration settings.
    $config = $this->configFactory->get('bi_formulary.settings');
    // Get config settings to disable/enable field.
    $state_coverage_enabled = $config->get('state_coverage');
    // Get all states.
    $states_options = $this->ftf->getStatesDropdownOptions();

    if (empty($form_state->getValue('states_dropdown'))) {
      // Use a default value.
      $selected_state = key($states_options);
      // Coverage default.
      $coverage = '';
    }
    else {
      // Get the value if it already exists.
      $selected_state = $form_state->getValue('states_dropdown');
      // Get coverage information.
      $coverage = $this->t('<span class="form-title">@coverage% @state_coverage_label</span><sup class="foot-note">@foot_note</sup>', [
        '@coverage' => $this->ftf->getStateCoverageInformation($selected_state),
        '@state_coverage_label' => 'Coverage',
        '@foot_note' => $parameter['disclaimer_message'],
      ]);
    }

    $form['states_fieldset'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Choose a state to begin'),
    ];

    $form['states_fieldset']['states_dropdown'] = [
      '#type' => 'select',
      '#title' => $this->t('State'),
      '#options' => $states_options,
      '#default_value' => $selected_state,
      '#ajax' => [
        'callback' => '::statesDropdownCallback',
        'wrapper' => 'products-fieldset-container',
      ],
    ];

    $form['states_fieldset']['find_location'] = [
      '#type' => 'button',
      '#title' => $this->t('Find Location'),
      '#value' => $this->t('Find Location'),
      '#ajax' => [
        'callback' => '::findLocationCallback',
      ],
    ];

    // Build the drugs and health plan field set.
    $form['products_fieldset_container'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'products-fieldset-container'],
    ];

    $form['products_fieldset_container']['products_fieldset'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Choose a health plan and drug'),
      '#states' => [
        'invisible' => [
          ':input[name=states_dropdown]' => ['value' => '0'],
        ],
      ],
    ];

    // Show the state coverage info.
    if ($state_coverage_enabled) {
      $form['products_fieldset_container']['products_fieldset']['state_coverage'] = [
        '#markup' => $coverage,
      ];
    }

    $form['products_fieldset_container']['products_fieldset']['health_plan'] = [
      '#type' => 'select',
      '#title' => $this->t('Health plan'),
      '#options' => $this->ftf->getHealthPlansDropdownOptions($selected_state),
      '#default_value' => !empty($form_state->getValue('health_plan')) ? $form_state->getValue('health_plan') : '',
    ];

    $form['products_fieldset_container']['products_fieldset']['drugs'] = [
      '#type' => 'select',
      '#title' => $this->t('Drug'),
      '#options' => $this->ftf->getDrugsDropdownOptions(),
      '#value' => $parameter['product_name'],
      '#access' => FALSE,
      '#attributes' => [
        'id' => 'select-drug',
      ],
    ];

    $form['products_fieldset_container']['products_fieldset']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#attributes' => [
        'type' => 'button',
      ],
      '#ajax' => [
        'callback' => '::successfulAjaxSubmit',
        'wrapper' => 'bi-formulary-results',
      ],
    ];

    // Container div where results will be shown.
    $form['formulary_results_container'] = [
      '#type' => 'container',
      '#attributes' => [
        'id' => 'bi-formulary-results',
        'class' => 'results-container',
      ],
    ];

    // State coverage footnote (if enabled).
    if ($state_coverage_enabled) {
      $form['products_fieldset_container']['products_fieldset']['state_coverage_footnote'] = [
        '#markup' => $this->t('<div class="formulary-footnote">@foot_note</div>', [
          '@foot_note' => $parameter['disclaimer_message'],
        ]),
      ];
    }

    // Disable the health plan and drug options until state is selected.
    if ($selected_state == '0') {
      $form['products_fieldset_container']['products_fieldset']['health_plan']['#disabled'] = TRUE;
      $form['products_fieldset_container']['products_fieldset']['submit']['#disabled'] = TRUE;
    }
    else {
      $form['products_fieldset_container']['products_fieldset']['health_plan']['#disabled'] = FALSE;
      $form['products_fieldset_container']['products_fieldset']['submit']['#disabled'] = FALSE;
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Void. Left empty as this is done in the AJAX handler.
  }

  /**
   * {@inheritDoc}
   */
  public function successfulAjaxSubmit(array $form, FormStateInterface $form_state) {

    $params = [];
    $params['health_plan_id'] = $form_state->getValue('health_plan');
    $params['health_plan_name'] = $form['products_fieldset_container']['products_fieldset']['health_plan']['#options'][$params['health_plan_id']];
    $params['state_id'] = $form_state->getValue('states_dropdown');
    $params['state_name'] = $form['states_fieldset']['states_dropdown']['#options'][$params['state_id']];
    $params['drug_id'] = $form_state->getValue('drugs');
    $params['drug_name'] = $form['products_fieldset_container']['products_fieldset']['drugs']['#options'][$params['drug_id']];

    // Container div where results will be shown.
    $build['formulary_results_container'] = [
      '#type' => 'container',
      '#attributes' => [
        // 'id' => 'bi-formulary-results',
        'class' => 'results-container',
      ],
      '#theme' => 'bi_formulary_results',
      '#download_link' => NULL,
      '#error' => NULL,
      '#no_results' => NULL,
      '#cache' => [
        'max-age' => 0,
      ],
    ];

    // Build the drugs and health plan field set.
    if ($params['drug_id'] == '0' || $params['state_id'] == 'none' || $params['health_plan_id'] == 'none') {
      // Return validation message.
      $build['formulary_results_container']['#error'] = $this->t('Please ensure all values are filled out!');
    }
    else {
      // Get the drug information.
      $formulary = $this->ftf->getDrug($params['drug_id'], $params['health_plan_id']);
      // If no results, display message.
      if (empty($formulary)) {
        // No results element.
        $build['formulary_results_container']['#no_results'] = $this->t('Sorry no results for this search');
      }
      // Return the information.
      else {
        $url = $formulary[0]['health_plan_url'];
        $build['formulary_results_container']['#download_link'] = $url;

      }

      // Dispatch event for Adobe DTM.
      $device = $this->deviceDetector->detect();
      // Merge device info and ftf form submission values.
      $client_request = array_merge($params, $device);
      // Create the new event ovject, merge params & device info array.
      $event = new FormularySubmitEventDtm($client_request);
      // Dispatch event.
      $this->eventDispatcher->dispatch(FormularySubmitEventDtm::EVENT_NAME, $event);

    }
    // Populate the selected option in our health plan select.
    $data = $this->renderer->render($build['formulary_results_container']);
    // Send AJAX response.
    $response = new AjaxResponse();
    $response->addCommand(new ReplaceCommand('#bi-formulary-results', $data));
    // Finally return the AjaxResponse object.
    return $response;
  }

  /**
   * Provide a new dropdown based on the AJAX call.
   *
   * This callback will occur *after* the form has been rebuilt by buildForm().
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   The portion of the render structure that will replace the
   *   health plan dropdown form element.
   */
  public function statesDropdownCallback(array $form, FormStateInterface $form_state) {
    return $form['products_fieldset_container'];
  }

  /**
   * {@inheritDoc}
   */
  public function findLocationCallback(array $form, FormStateInterface $form_state) {
    // Send AJAX response.
    $response = new AjaxResponse();
    $response->addCommand(new InvokeCommand('html', 'trigger', ['updateSelect']));
    // Finally return the AjaxResponse object.
    return $response;
  }

}
