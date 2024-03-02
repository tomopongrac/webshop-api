<?php

declare(strict_types=1);

namespace TomoPongrac\WebshopApiBundle\Controller;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use TomoPongrac\WebshopApiBundle\DTO\ListProductsQueryParameters;
use TomoPongrac\WebshopApiBundle\DTO\PaginationResponse;
use TomoPongrac\WebshopApiBundle\Entity\UserWebShopApiInterface;
use TomoPongrac\WebshopApiBundle\Repository\ProductRepository;

class ListProductsController
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly SerializerInterface $serializer,
        private readonly RequestStack $requestStack,
        private readonly DenormalizerInterface $denormalizer,
        private readonly Security $security,
    ) {
    }

    #[Route('/products', name: 'get_products', methods: ['GET'])]
    public function __invoke(): Response
    {
        /** @var UserWebShopApiInterface $user */
        $user = $this->security->getUser();

        $queryParameters = $this->requestStack->getCurrentRequest()?->query->all();

        $listProductsQueryParameters = $this->denormalizer->denormalize(
            $queryParameters,
            ListProductsQueryParameters::class,
            null,
            [
                'groups' => ['product:list-query-parameters'],
            ]
        );

        $productsResponse = $this->productRepository->getProducts($listProductsQueryParameters, $user);

        $jsonResponse = (new PaginationResponse())
            ->setCurrentPage((int) $listProductsQueryParameters->getPage())
            ->setTotalPages((int) ceil($productsResponse[1] / (int) $listProductsQueryParameters->getLimit()))
            ->setTotalResults($productsResponse[1])
            ->setLimit((int) $listProductsQueryParameters->getLimit())
            ->setData($productsResponse[0]);

        return new JsonResponse($this->serializer->serialize($jsonResponse, 'json', [
            'groups' => ['product:list'],
        ]), Response::HTTP_OK, [], true);
    }
}
