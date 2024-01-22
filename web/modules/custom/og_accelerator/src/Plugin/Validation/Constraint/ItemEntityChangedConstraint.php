<?php

namespace Drupal\og_accelerator\Plugin\Validation\Constraint;

use Drupal\Core\Entity\Plugin\Validation\Constraint\EntityChangedConstraint;
use Symfony\Component\Validator\Constraint;

/**
 * Validation constraint for the Item entity changed timestamp.
 *
 * @Constraint(
 *   id = "ItemEntityChanged",
 *   label = @Translation("Item entity changed", context = "Validation"),
 *   type = {"entity"}
 * )
 */
class ItemEntityChangedConstraint extends EntityChangedConstraint
{
}
