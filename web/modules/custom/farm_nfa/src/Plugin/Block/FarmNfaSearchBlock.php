<?php

namespace Drupal\farm_nfa\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\farm_nfa\Form\FarmNfaSearchBlockForm;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a Farm NFA search block.
 *
 * @Block(
 *   id = "farm_nfa_dashboard_search_form_block",
 *   admin_label = @Translation("Farm NFA dashboard search form"),
 *   category = @Translation("Farm NFA")
 * )
 */
class FarmNfaSearchBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The form builder.
   *
   * @var \Drupal\Core\Form\FormBuilderInterface
   */
  protected $formBuilder;

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
   * Constructs a new FarmNfaSearchBlock.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Form\FormBuilderInterface $form_builder
   *   The form builder.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, FormBuilderInterface $form_builder, ConfigFactoryInterface $config_factory, RendererInterface $renderer) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->formBuilder = $form_builder;
    $this->configFactory = $config_factory;
    $this->renderer = $renderer;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static($configuration, $plugin_id, $plugin_definition,
      $container->get('form_builder'),
      $container->get('config.factory'),
      $container->get('renderer')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $route = $this->configuration['route'] ?? NULL;
    $entity_type = $this->configuration['entity_type'] ?? NULL;
    if (empty($route)) {
      return [];
    }

    // Create the form object to set an unique form id per entity type.
    $form = new FarmNfaSearchBlockForm($entity_type, $this->configFactory, $this->renderer);
    // Render the Farm NFA search form with route and entity type as parameters
    // which they will be used in the autocomplete json api controller.
    return $this->formBuilder->getForm($form, $route, $entity_type);
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['route'] = [
      '#type' => 'textfield',
      '#title' => $this->t('View route'),
      '#description' => $this->t('The view page route where the form submits to.'),
      '#default_value' => $this->configuration['route'],
      '#required' => TRUE,
    ];

    $form['entity_type'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Entity type'),
      '#description' => $this->t('This generates an autocomplete with the content of the selected entity bundles.'),
      '#default_value' => $this->configuration['entity_type'],
      '#required' => TRUE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['route'] = $form_state->getValue('route');
    $this->configuration['entity_type'] = $form_state->getValue('entity_type');
  }

}
