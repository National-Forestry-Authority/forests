<?php

namespace Drupal\farm_nfa\Plugin\Field\FieldWidget;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldWidget\OptionsSelectWidget;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Security\TrustedCallbackInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines the Program/activity & sub-activity field widget.
 *
 * @FieldWidget(
 *   id = "farm_nfa_subactivity",
 *   label = @Translation("NFA: Program/Activity"),
 *   field_types = {"list_string"},
 * )
 */
class SubactivityWidget extends OptionsSelectWidget implements TrustedCallbackInterface {
  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a SubactivityWidget object.
   *
   * @param string $plugin_id
   *   The plugin id.
   * @param mixed $plugin_definition
   *   The plugin definition.
   * @param mixed $field_definition
   *   The field definition.
   * @param array $settings
   *   The settings array.
   * @param array $third_party_settings
   *   The third party settings array.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings);
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
      $configuration['third_party_settings'],
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $selected_subactivity = NULL;

    // Get the selected sub-activity from the triggering element.
    $user_input = $form_state->getUserInput();
    $triggering_element_name = $user_input['_triggering_element_name'] ?? NULL;
    if ($triggering_element_name) {
      $parts = explode('[', str_replace(']', '', $triggering_element_name));
      $selected_subactivity = NestedArray::getValue($user_input, $parts);
    }

    $wrapper_id = "subactivity-wrapper";
    $widget['activity'] = [
      '#type' => 'select',
      '#title' => $this->t('Activity'),
      '#options' => self::getActivityOptions(),
      '#empty_option' => $this->t('- None -'),
      '#default_value' => $items[$delta]->activity ?? NULL,
      '#ajax' => [
        'callback' => [$this, 'updateSubActivityOptions'],
        'wrapper' => $wrapper_id,
      ],
    ];

    // The sub-activity options are loaded from the selected CFR program fields.
    $asset_id = !empty($form_state->getValue('cfr')) ? reset($form_state->getValue('cfr'))['target_id'] : NULL;

    $widget['sub_activity'] = parent::formElement($items, $delta, $element, $form, $form_state);
    $widget['sub_activity']['#options'] = self::getSubActivityOptions($selected_subactivity, $asset_id);
    $widget['sub_activity']['#empty_option'] = $this->t('- None -');
    $widget['sub_activity']['#default_value'] = $selected_subactivity;
    $widget['sub_activity']['#prefix'] = '<div id="' . $wrapper_id . '">';
    $widget['sub_activity']['#suffix'] = '</div>';

    return $widget;
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    return $values['sub_activity'] ?? [];
  }

  /**
   * Helper function to get activity options.
   */
  public function getActivityOptions() {
    // Load the CFR form display config entity and get the list of program tabs.
    $form_display = $this->entityTypeManager->getStorage('entity_form_display')->load('asset.cfr.default');
    $field_groups = $form_display->getThirdPartySettings('field_group');
    $options = [];

    foreach ($field_groups as $field_group) {
      if ($field_group['format_type'] == 'program_tab') {
        $programs = [];
        foreach ($field_group['children'] as $program) {
          $program_field = $this->entityTypeManager->getStorage('field_config')->load('asset.cfr.' . $program);
          $programs[$program] = $program_field->label();
        }
        $options[$field_group['label']] = $programs;
      }
    }

    return $options;
  }

  /**
   * Ajax callback to update sub-activity options.
   */
  public function updateSubActivityOptions(array $form, FormStateInterface $form_state) {
    return $form['sub_activity']['widget'][0]['sub_activity'];
  }

  /**
   * Helper function to get sub-activity options.
   */
  public function getSubActivityOptions($activity, $asset_id) {
    // Load the sub-activities based on the selected activity and CFR.
    $subActivities = [];

    if (!empty($activity) && !empty($asset_id)) {
      // Load the activity summaries from the CFR.
      $cfr = $this->entityTypeManager->getStorage('asset')->load($asset_id);
      $summaries = $cfr->get($activity)->getValue();
      if (!empty($summaries)) {
        foreach ($summaries as $delta => $summary) {
          // The option key has to uniquely identify the CFR, the sub-activity
          // and the sub-activity delta.
          $key = "$asset_id:$activity:$delta";
          $subActivities[$key] = $summary['summary'];
        }
      }
    }
    return $subActivities;
  }

  /**
   * {@inheritdoc}
   */
  public static function trustedCallbacks() {
    return ['preRenderOptions'];
  }

}
