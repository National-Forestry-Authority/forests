<?php

namespace Drupal\farm_nfa\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Datetime\DateTime;
use Drupal\key\KeyRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Forest plan gfw form.
 *
 * @ingroup farm_nfa
 */
class ForestPlanGfwForm extends FormBase {

  /**
   * The current route match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * The current request.
   *
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected $request;
  
  /**
   * The key repository.
   *
   * @var \Drupal\key\KeyRepositoryInterface
   */
  protected $keyRepository;

  /**
   * Constructs a new ForestPlanGfwForm.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $routeMatch
   *   The current route match.
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request.
   */
  public function __construct(RouteMatchInterface $routeMatch, Request $request, KeyRepositoryInterface $keyRepository) {
    $this->routeMatch = $routeMatch;
    $this->request = $request;
    $this->keyRepository = $keyRepository;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('current_route_match'),
      $container->get('request_stack')->getCurrentRequest(),
      $container->get('key.repository')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'farm_nfa_forest_budget_plan_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Set the form title.
    $form['#title'] = $this->t('GFW');
    $asset = $this->routeMatch->getParameter('asset');
    $assetType = '';
    $landType = '';
    if ($asset) {
      $assetType = $asset->bundle();
    }
    if ($asset && $asset->hasField('land_type') && !$asset->get('land_type')->isEmpty()) {
      $landType = $asset->get('land_type')->value;
    }
    $gfw_api_user = $this->keyRepository->getKey('gfw_api_user');
    $gfw_api_password = $this->keyRepository->getKey('gfw_api_password');
    $gfw_api_user = $gfw_api_user ? $gfw_api_user->getKeyValue() : '';
    $gfw_api_password = $gfw_api_password ? $gfw_api_password->getKeyValue() : '';
    $gfw_api_key = $this->generateGfwApiKey('https://data-api.globalforestwatch.org', ['username' => $gfw_api_user, 'password' => $gfw_api_password]);
    error_log(print_r(is_string($gfw_api_key), TRUE));
    error_log(print_r('testing 123', TRUE));
    $form['gfw_map'] = [
      '#type' => 'farm_map',
      '#map_type' => 'farm_nfa_plan_locations',
      '#map_settings' => [
        'plan' => $this->routeMatch->getRawParameter('plan'),
        'asset' => $this->routeMatch->getRawParameter('asset'),
        'host' => $this->request->getHost(),
        'asset_type' => $assetType,
        'land_type' => $landType,
        'gfw_api_key' => $gfw_api_key,
      ],
      '#attached' => [
        'library' => [
          'farm_nfa/behavior_farm_nfa_gfw_layers',
        ],
      ],
    ];

    $form['range'] = [
      '#type' => 'daterangepicker',
      '#prefix' => '<div class="daterange-picker"><div class="field__label">' . $this->t('GFW alerts date range') . '</div>',
      '#suffix' => '</div>',
      '#DateRangePickerOptions' => [
        'initial_text' => $this->t('Select date range...'),
        'apply_button_text' => $this->t('Apply'),
        'clear_button_text' => $this->t('Clear'),
        'cancel_button_text' => $this->t('Cancel'),
        'range_splitter' => ' - ',
        'date_format' => 'd M, yy',
        // This needs to be a format recognised by javascript Date.parse method.
        'alt_format' => 'yy-mm-dd',
        'date_picker_options' => [
          'number_of_months' => 2,
        ],
      ],
    ];

    $form['datepicker_help'] = [
      '#type' => 'markup',
      '#markup' => t('Click to select the date range'),
      '#prefix' => '<div class="daterange-picker-help">',
      '#suffix' => '</div>',
    ];

    return $form;
  }
  
  /**
   * Fetches data from the GFW API.
   *
   * @param string $endpoint
   *   The API endpoint to call.
   * @param array $options
   *   An optional array of options to pass to the HTTP client.
   *
   * @return string|null
   *   The API Key as a string, or NULL on failure.
   */
  private function generateGfwApiKey(string $endpoint, array $options = []) {
    try {
      if (empty($options['username']) || empty($options['password'])) {
        return NULL;
      }
      // Generate Auth Token
      $client = \Drupal::httpClient();
      // 'e08964ab-1bc0-4b29-b6fe-0d2de9b522d1'
      $params = [
        'username' => $options['username'],
        'password' => $options['password'],
        'grant_type' => 'password',
      ];
      // Make the POST request with x-www-form-urlencoded data.
      $response = $client->post($endpoint.'/auth/token', [
        'form_params' => $params,
        'headers' => [
          'Content-Type' => 'application/x-www-form-urlencoded',
        ],
      ]);
      $response = json_decode($response->getBody(), TRUE);
      $accessToken = $response['data']['access_token'];
      // Make the GET request with the token tp generate API key.
      $queryParams = [
        'alias' => 'nfa-api-key-'.time(),
        'organization' => 'nfa',
        'email' => $options['username'],
      ];
      error_log(print_r($accessToken, true));
      $response = $client->post($endpoint.'/auth/apikey', [
        'headers' => [
          'Authorization' => 'Bearer ' . $accessToken,
          'Content-Type' => 'application/json',
        ],
        'json' => $queryParams,
      ]);
      $response = json_decode($response->getBody(), TRUE);
      $api_key = $response['data']['api_key'];
      error_log(print_r($api_key, true));
      return $api_key;
    }
    catch (\Exception $e) {
      // Log the error and return NULL.
      \Drupal::logger('farm_nfa')->error('GFW API call failed: @message', ['@message' => $e->getMessage()]);
      return NULL;
    }
  }
  
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

  }

}
