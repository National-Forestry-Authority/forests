<?php

namespace Drupal\farm_nfa_config_update\Form;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configure overrides of farmOS, core and contrib configuration.
 */
class SettingsForm extends ConfigFormBase implements ContainerInjectionInterface {

  /**
   * Config settings.
   *
   * @var string
   */
  const SETTINGS = 'farm_nfa_config_update.settings';

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
    return 'farm_nfa_config_update_settings';
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
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config(static::SETTINGS);

    $form['config_overrides'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Overridden configuration items'),
      '#description' => $this->t('List configuration items that are overridden and should not be reverted. Enter one config item per line.'),
      '#default_value' => $config->get('config_overrides'),
    ];

    $description = $this->t('List missing and inactive optional configuration items that are known and can be excluded from the configuration update report.');
    $form['config_exclusions'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Excluded configuration items'),
      '#description' => $this->t('List missing or inactive configuration items that are known and can be excluded from the configuration update report.'),
      '#default_value' => $config->get('config_exclusions'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Save the config value.
    $this->configFactory->getEditable(static::SETTINGS)
      ->set('config_overrides', $form_state->getValue('config_overrides'))
      ->set('config_exclusions', $form_state->getValue('config_exclusions'))
      ->save();
    parent::submitForm($form, $form_state);

    // Ensure the routes are rebuilt when overriding them.
    $this->routerBuilder->rebuild();
  }

}
