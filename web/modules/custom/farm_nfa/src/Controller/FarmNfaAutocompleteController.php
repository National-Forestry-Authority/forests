<?php

namespace Drupal\farm_nfa\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\Element\EntityAutocomplete;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Autocomplete controller for the custom dashboard blocks.
 */
class FarmNfaAutocompleteController extends ControllerBase {

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a FarmNfaAutocompleteController object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('entity_type.manager'));
  }

  /**
   * Creates the autocomplete results for a textfield.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request instance.
   * @param string $entity_type
   *   The entity type which is used to filter values.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The JSON generated with the autocomplete values.
   */
  public function handleAutocomplete(Request $request, string $entity_type) {
    $results = [];
    $storage = $this->entityTypeManager->getStorage($entity_type);
    $input = $request->query->get('q');
    if (!$input) {
      return new JsonResponse($results);
    }
    $input = Xss::filter($input);
    $query = $storage->getQuery()
      ->condition('name', $input, 'CONTAINS')
      ->groupBy('nid')
      ->sort('created', 'DESC')
      ->range(0, 10);
    $ids = $query->execute();
    $entities = $ids ? $storage->loadMultiple($ids) : [];
    foreach ($entities as $entity) {
      $results[] = [
        'value' => EntityAutocomplete::getEntityLabels([$entity]),
        'url' => $entity->toUrl()->toString(),
        'label' => $entity->label(),
      ];
    }

    return new JsonResponse($results);
  }

}
