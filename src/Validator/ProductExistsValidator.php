<?php

namespace TomoPongrac\WebshopApiBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use TomoPongrac\WebshopApiBundle\Entity\Product;
use TomoPongrac\WebshopApiBundle\Repository\ProductRepository;

class ProductExistsValidator extends ConstraintValidator
{
    public function __construct(
        private readonly ProductRepository $productRepository,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ProductExists) {
            throw new \InvalidArgumentException(sprintf('The "%s" constraint is not supported', get_class($constraint)));
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_array($value) && !($value instanceof \Traversable)) {
            return;
        }

        foreach ($value as $product) {
            // Assume $product has a method getId() that returns product's id
            /** @var ?Product $product */
            $product = $this->productRepository->find($product->getProductId());
            if (null === $product || null === $product->getPublishedAt()) {
                // Add violation if product does not exist
                $this->context->buildViolation($constraint->message)
                    ->addViolation();
            }
        }
    }
}
