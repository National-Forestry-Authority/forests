<?php

namespace Drupal\farm_nfa_planting\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Routing\RouteProviderInterface;
use Drupal\farm_location\AssetLocationInterface;
use Drupal\farm_nfa\Form\ForestPlanBaseForm;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Forest plan planting form.
 *
 * @ingroup farm_nfa_planting
 */
class ForestPlanPlantingForm extends ForestPlanBaseForm {

  /**
   * The asset location service.
   *
   * @var \Drupal\farm_location\AssetLocationInterface
   */
  protected $assetLocation;

  /**
   * Constructs a new ForestPlanPlantingForm.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The HTTP request.
   * @param \Drupal\farm_location\AssetLocationInterface $asset_location
   *   The asset location service.
   */
  public function __construct(Request $request, RouteProviderInterface $route_provider, RouteMatchInterface $route_match, AssetLocationInterface $asset_location) {
    parent::__construct($request, $route_provider, $route_match);
    $this->assetLocation = $asset_location;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('request_stack')->getCurrentRequest(),
      $container->get('router.route_provider'),
      $container->get('current_route_match'),
      $container->get('asset.location'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'farm_nfa_planting_plan_form';
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() : array {
    return [
        'log_type' => 'planting',
        'form_title' => t('Planting'),
      ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    /** @var \Drupal\log\Entity\LogInterface $log */
    $log = $form['#log'];
    if (!$log->isNew() && isset($form['quantity'])) {
      foreach ($log->get('quantity')->referencedEntities() as $quantity) {
        if ($quantity->get('units')->entity->get('name')->value == 'trees') {
          $total_trees = $quantity->get('value')->value;
        }
        if ($quantity->get('units')->entity->get('name')->value == 'hectares') {
          $hectares = $quantity->get('value')->value;
        }
      }
      if (!empty($total_trees) && !empty($hectares)) {
        $form['seedlings_per_hectare'] = [
          '#type' => 'markup',
          '#markup' => $this->t('Number of seedlings per hectares: %value', ['%value' => round($total_trees / $hectares, 2)]),
          '#weight' => $form['quantity']['#weight'] + 1,
        ];
      }
    }

    // Only forests available to reference as assets.
    $form['asset']['widget']['actions']['bundle']['#options'] = ['forest' => $this->t('Forest')];
    $form['asset']['widget']['actions']['bundle']['#access'] = FALSE;

    /** @var \Drupal\plan\Entity\PlanInterface $plan */
    $plan = $form['#plan'];
    $form['asset']['widget']['actions']['ief_add']['#value'] = $this->t('Add new forest');
    if ($plan->bundle() == 'plantation') {
      $form['asset']['widget']['actions']['ief_add']['#value'] = $this->t('Add new stand');
    }

    $form['asset']['widget']['actions']['ief_add']['#access'] = empty(Element::children($form['asset']['widget']['entities']));

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    /** @var \Drupal\plan\Entity\PlanInterface $plan */
    $plan = $form['#plan'];
    if ($plan->bundle() == 'plantation') {
      /** @var \Drupal\log\Entity\LogInterface $log */
      $log = $form['#log'];
      $referenced_assets = $this->assetLocation->getAssetsByLocation($log->get('location')->referencedEntities());
      $referenced_assets = array_filter($referenced_assets, fn($asset) => $asset->bundle() === 'forest');

      if ($log->isNew() && !empty($referenced_assets)) {
        $form_state->setError($form, $this->t('Only one stand can be planted in a compartment at a time. Please choose a different compartment.'));
      }
      if (!$log->isNew()) {
        $current_assets = $log->get('asset')->referencedEntities();
        $current_assets = array_filter($current_assets, fn($asset) => $asset->bundle() === 'forest');
        foreach ($current_assets as $current_asset) {
          if (!in_array($current_asset->id(), array_keys($referenced_assets))) {
            $form_state->setError($form, $this->t('Only one stand can be planted in a compartment at a time. Please choose a different compartment.'));
          }
        }
      }
    }
  }

}
