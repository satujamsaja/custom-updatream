<?php

namespace Drupal\og_accelerator\Plugin\Validation\Constraint;

use Drupal\og_accelerator\Entity\ItemEntityInterface;
use Drupal\Core\Entity\Plugin\Validation\Constraint\EntityChangedConstraintValidator;
use Symfony\Component\Validator\Constraint;

/**
 * Validates the ItemEntityEntityChanged constraint.
 */
class ItemEntityChangedConstraintValidator extends EntityChangedConstraintValidator
{
  /**
   * {@inheritdoc}
   */
  public function validate($entity, Constraint $constraint)
  {
    if ($entity instanceof ItemEntityInterface) {
      return;
    }
    parent::validate($entity, $constraint);
  }
}
