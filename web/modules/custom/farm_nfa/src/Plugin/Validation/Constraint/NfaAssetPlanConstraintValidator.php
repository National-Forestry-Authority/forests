<?php

namespace Drupal\farm_nfa\Plugin\Validation\Constraint;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\plan\Entity\PlanInterface;
use Drupal\views\ViewExecutable;
use Drupal\views\Views;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates that an asset belongs to just one plan that is not archived.
 */
class NfaAssetPlanConstraintValidator extends ConstraintValidator implements ContainerInjectionInterface {

  /**
   * Creates a NfaAssetPlanConstraintValidator object.
   *
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $entityFieldManager
   *   The entity type manager.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   */
  public function __construct(protected EntityFieldManagerInterface $entityFieldManager, protected EntityTypeManagerInterface $entityTypeManager) {
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_field.manager'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function validate($value, Constraint $constraint) {
    /** @var \Drupal\Core\Field\EntityReferenceFieldItemList $value */
    /** @var \Drupal\farm_nfa\Plugin\Validation\Constraint\NfaAssetPlanConstraint $constraint */
    /** @var \Drupal\Core\Entity\EntityInterface $entity */
    $entity = $value->getEntity();

    if (!$entity instanceof PlanInterface) {
      return;
    }

    $args = [$entity->id()];
    $assets_in_use = $this->getAssetsInUse($args);
    if (empty($assets_in_use)) {
      return;
    }

    foreach ($value as $delta => $item) {
      $target_id = $item->target_id;
      if (in_array($target_id, $assets_in_use)) {
        $this->context->addViolation($constraint->message, [
          '@entity_type' => $entity->getEntityType()->getSingularLabel(),
          '@field_name' => $value->getFieldDefinition()->getLabel(),
          '%value' => $target_id,
        ]);
      }
    }
  }

  /**
   * Get the assets in use.
   *
   * @TODO: this is duplicated and should be moved elsewhere.
   * @see \Drupal\farm_nfa_natural_forest\Plugin\Field\FieldWidget\PlanSectorCFR::getAssetsInUse
   *
   * @param array $args
   *   The arguments.
   *
   * @return array
   *   The assets in use.
   */
  protected function getAssetsInUse(array $args): array {
    // @todo the views should be selectable in the widget settings.
    $view = Views::getView('assets_in_use');
    if ($view instanceof ViewExecutable) {
      $view->setDisplay('default');
      $view->setArguments($args);
      $view->preExecute();
      $view->execute();
      return array_column($view->result, 'asset_field_data_id');
    }

    return [];
  }

}
