<?php

namespace Drupal\farm_nfa_cfr\Plugin\TypedRepositories;

use Drupal\typed_entity\Annotation\TypedRepository;
use Drupal\typed_entity\TypedRepositories\TypedRepositoryBase;

/**
 * The repository for CFR assets.
 *
 * @TypedRepository(
 *   entity_type_id = "asset",
 *   bundle = "cfr",
 *   wrappers = @ClassWithVariants(
 *     fallback = "Drupal\farm_nfa_cfr\WrappedEntities\Cfr",
 *   ),
 *   description = @Translation("Repository that holds business logic applicable to all CFR assets.")
 * )
 */
final class CfrRepository extends TypedRepositoryBase {

}
