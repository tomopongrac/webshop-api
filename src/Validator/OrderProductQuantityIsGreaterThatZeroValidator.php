<?php

namespace TomoPongrac\WebshopApiBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class OrderProductQuantityIsGreaterThatZeroValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof OrderProductQuantityIsGreaterThatZero) {
            throw new \InvalidArgumentException(sprintf('The "%s" constraint is not supported', get_class($constraint)));
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_array($value) && !($value instanceof \Traversable)) {
            return;
        }

        foreach ($value as $product) {
            if ($product->getQuantity() <= 0) {
                $this->context->buildViolation($constraint->message)
                    ->addViolation();
            }
        }
    }
}
