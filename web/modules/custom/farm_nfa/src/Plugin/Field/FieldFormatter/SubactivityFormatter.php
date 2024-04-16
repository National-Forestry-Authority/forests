<?php

declare(strict_types=1);

namespace Drupal\farm_nfa\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\Annotation\FieldFormatter;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'Activity/Sub-activity' formatter.
 *
 * @FieldFormatter(
 *   id = "subactivity",
 *   label = @Translation("Activity/Sub-activity"),
 *   field_types = {"string"},
 * )
 */
final class SubactivityFormatter extends FormatterBase {
  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a SubactivityFormatter object.
   *
   * @param string $plugin_id
   *   The plugin_id for the formatter.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The definition of the field to which the formatter is associated.
   * @param array $settings
   *   The formatter settings.
   * @param string $label
   *   The formatter label display setting.
   * @param string $view_mode
   *   The view mode.
   * @param array $third_party_settings
   *   Any third party settings.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *    The entity type manager.
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, $label, $view_mode, array $third_party_settings, $entity_type_manager) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $elements = [];

    foreach ($items as $delta => $item) {
      // Split the value into its components.
      [$entity_type, $entity_id, $activity, $subactivity_delta] = explode(':', $item->value);

      // Load the entity.
      $entity = $this->entityTypeManager->getStorage($entity_type)->load($entity_id);

      // Get the activity and subactivity values.
      $form_display_stub = $entity_type == 'asset' ? 'asset.cfr.' : 'plan.natural.';

      $activity_label = $this->entityTypeManager->getStorage('field_config')->load($form_display_stub . $activity);
      $subactivity = $entity->get($activity)->getValue()[$subactivity_delta]['summary'];

      // Add to the render array.
      $elements[$delta] = [
        '#markup' => $this->t('@activity > @subactivity', [
          '@activity' => $activity_label->label(),
          '@subactivity' => $subactivity,
        ]),
      ];
    }

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(FieldDefinitionInterface $field_definition) {
    // Applies to log entities of type Activity.
    return $field_definition->getTargetEntityTypeId() === 'log' && $field_definition->getTargetBundle() === 'activity';
  }

}
