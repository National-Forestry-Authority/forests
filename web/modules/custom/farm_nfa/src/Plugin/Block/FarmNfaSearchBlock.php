<?php

namespace Drupal\farm_nfa\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\farm_nfa\Form\FarmNfaSearchBlockForm;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a Farm NFA search block.
 *
 * @Block(
 *   id = "farm_nfa_search_form_block",
 *   admin_label = @Translation("Farm NFA search form"),
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
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, FormBuilderInterface $form_builder) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->formBuilder = $form_builder;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static($configuration, $plugin_id, $plugin_definition,
      $container->get('form_builder'),
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

    // Render the Farm NFA search form with route and entity type as parameters
    // which they will be used in the autocomplete json api controller.
    return $this->formBuilder->getForm(FarmNfaSearchBlockForm::class, $route, $entity_type);
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
    ];

    $form['entity_type'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Entity type'),
      '#description' => $this->t('This generates an autocomplete with the content of the selected entity bundles.'),
      '#default_value' => $this->configuration['entity_type'],
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
