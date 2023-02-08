<?php

namespace Drupal\farm_nfa_views\Form;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configure overrides of FarmOS views.
 */
class SettingsForm extends ConfigFormBase implements ContainerInjectionInterface {

  /**
   * Config settings.
   *
   * @var string
   */
  const SETTINGS = 'farm_nfa_views.settings';

  /**
   * The router builder service.
   *
   * @var \Drupal\Core\Routing\RouteBuilderInterface
   */
  protected $routerBuilder;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->routerBuilder = $container->get('router.builder');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'farm_nfa_views_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      static::SETTINGS,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    $list = array_filter(preg_split("/(\r\n|\n|\r)/", $form_state->getValue('overridden_routes')));
    foreach ($list as $route_pair) {
      $matches = [];
      if (preg_match('/(.*)\|(.*)/', $route_pair, $matches)) {
        // Trim key and value to avoid unwanted spaces issues.
        $override = 'views.view.' . trim($matches[2]);
        // Check that the override view exists.
        if ($this->configFactory->get($override)->isNew()) {
          $form_state->setErrorByName('enabled_routes', $this->t('@override view does not exist.', ['@override' => $override]));
        }
      }
      else {
        $form_state->setErrorByName('enabled_routes', $this->t('@pair is not valid. The format to use is route|view_id.', ['@pair' => $route_pair]));
      }

    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config(static::SETTINGS);

    $description = $this->t('Specify route names to be overridden by Farm NFA views. Enter one override per line in the format route|view_id.');
    $description .= '<br/>' . $this->t('For example entity.asset.collection|nfa_farm_asset');

    $form['overridden_routes'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Overridden routes'),
      '#description' => $description,
      '#default_value' => $config->get('overridden_routes'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Save the config value.
    $this->configFactory->getEditable(static::SETTINGS)
      ->set('overridden_routes', $form_state->getValue('overridden_routes'))
      ->save();
    parent::submitForm($form, $form_state);

    // Ensure the routes are rebuilt when overriding them.
    $this->routerBuilder->rebuild();
  }

}
