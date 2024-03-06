<?php

namespace Drupal\farm_nfa\Plugin\Field\FieldType;

use Drupal\Component\Utility\Random;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Defines the 'farm_nfa_program' field type.
 *
 * @FieldType(
 *   id = "farm_nfa_program",
 *   label = @Translation("Program"),
 *   category = @Translation("General"),
 *   default_widget = "farm_nfa_program",
 *   default_formatter = "farm_nfa_program_default"
 * )
 */
class ProgramItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    if ($this->summary !== NULL) {
      return FALSE;
    }
    elseif ($this->description !== NULL) {
      return FALSE;
    }
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {

    $properties['summary'] = DataDefinition::create('string')
      ->setLabel(t('Summary'));
    $properties['description'] = DataDefinition::create('string')
      ->setLabel(t('Description'));

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function getConstraints() {
    $constraints = parent::getConstraints();

    $options['summary']['NotBlank'] = [];

    $constraint_manager = \Drupal::typedDataManager()->getValidationConstraintManager();
    $constraints[] = $constraint_manager->create('ComplexData', $options);
    // @todo Add more constraints here.
    return $constraints;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {

    $columns = [
      'summary' => [
        'type' => 'varchar',
        'length' => 255,
      ],
      'description' => [
        'type' => 'text',
        'size' => 'big',
      ],
    ];

    $schema = [
      'columns' => $columns,
      // @DCG Add indexes here if necessary.
    ];

    return $schema;
  }

  /**
   * {@inheritdoc}
   */
  public static function generateSampleValue(FieldDefinitionInterface $field_definition) {

    $random = new Random();

    $values['summary'] = $random->word(mt_rand(1, 255));

    $values['description'] = $random->paragraphs(5);

    return $values;
  }

}
