<?php

namespace Drupal\farm_nfa\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Url;

/**
 * Builds the Farm NFA search form for the Farm NFA search block.
 */
class FarmNfaSearchBlockForm extends FormBase {

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * The view route to redirect.
   *
   * @var string
   */
  protected $viewRoute;

  /**
   * The form type defined in the block to generates an id per entity type.
   *
   * @var string
   */
  protected $formType;

  /**
   * Constructs a new SearchBlockForm.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer.
   */
  public function __construct($form_type, ConfigFactoryInterface $config_factory, RendererInterface $renderer) {
    $this->configFactory = $config_factory;
    $this->renderer = $renderer;
    $this->formType = $form_type;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'farm_nfa_' . $this->formType . '_search_block_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $route = NULL, $entity_type = NULL) {
    $this->viewRoute = $route;

    $form['search'] = [
      '#type' => 'textfield',
      '#attributes' => [
        'autocomplete' => 'off',
      ],
    ];

    // If the entity type is defined then add autocomplete functionality.
    if (!empty($entity_type)) {
      $form['search']['#autocomplete_route_name'] = 'farm_nfa.search_autocomplete';
      $form['search']['#autocomplete_route_parameters'] = ['entity_type' => $entity_type];
      $form['search']['#attributes']['data-farm-nfa-autocomplete-search'] = $this->getFormId();
      $form['#attached']['library'][] = 'farm_nfa/search_autocomplete';
    }

    $form['actions'] = ['#type' => 'actions'];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Search'),
      '#button_type' => 'primary',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $form_state_values = $form_state->getValues();
    // Redirect to the route filtering by the parameters introduced.
    if (isset($form_state_values['search'])) {
      $redirect_params['name'] = $form_state_values['search'];
      $url = Url::fromRoute($this->viewRoute, $redirect_params);
      $form_state->setRedirectUrl($url);
    }
  }

}
