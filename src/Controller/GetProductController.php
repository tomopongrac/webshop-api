<?php

declare(strict_types=1);

namespace TomoPongrac\WebshopApiBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use TomoPongrac\WebshopApiBundle\Repository\ProductRepository;

class GetProductController
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly SerializerInterface $serializer,
    ) {
    }

    #[Route('/products/{id}', name: 'get_product', methods: ['GET'])]
    public function __invoke(int $id): Response
    {
        $product = $this->productRepository->getSingleProduct($id);

        if (null === $product) {
            throw new NotFoundHttpException('Product not found');
        }

        return new JsonResponse($this->serializer->serialize($product, 'json', [
            'groups' => ['product:read'],
        ]), Response::HTTP_OK, [], true);
    }
}
