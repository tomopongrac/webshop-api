<?php

declare(strict_types=1);

namespace TomoPongrac\WebshopApiBundle\Serializer;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use TomoPongrac\WebshopApiBundle\Entity\Product;

class CustomObjectNormalizer implements NormalizerInterface
{
    public function __construct(
        #[Autowire(service: 'serializer.normalizer.object')]
        private readonly NormalizerInterface $normalizer,
    ) {
    }

    public function normalize(
        mixed $object,
        ?string $format = null,
        array $context = []
    ): array|string|int|float|bool|\ArrayObject|null {
        $data = $this->normalizer->normalize($object, $format, $context);

        if ($object instanceof Product && isset($data['price'])) {
            $data['price'] = [
                'amount' => number_format($object->getPrice() / 100, 2),
                'currency' => 'EUR',
            ];
        }

        return ['data' => $data];
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Product;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Product::class => true,
        ];
    }
}
