<?php

declare(strict_types=1);

namespace TomoPongrac\WebshopApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use TomoPongrac\WebshopApiBundle\DTO\FilterProductsRequest;
use TomoPongrac\WebshopApiBundle\DTO\PaginationResponse;
use TomoPongrac\WebshopApiBundle\Entity\UserWebShopApiInterface;
use TomoPongrac\WebshopApiBundle\Repository\ProductRepository;

class FilterProductsController extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly Security $security,
        private readonly ProductRepository $productRepository,
    ) {
    }

    #[Route('/products/filter', name: 'filter_products', methods: ['POST'])]
    public function __invoke(Request $request): Response
    {
        /** @var FilterProductsRequest $filterProductsRequest */
        $filterProductsRequest = $this->serializer->deserialize($request->getContent(), FilterProductsRequest::class, 'json', [
            'groups' => ['filterProducts:request'],
        ]);

        /** @var UserWebShopApiInterface $user */
        $user = $this->security->getUser();

        $productsResponse = $this->productRepository->filterProducts($filterProductsRequest, $user);

        $jsonResponse = (new PaginationResponse())
            ->setCurrentPage($filterProductsRequest->getPagination()->getPage())
            ->setTotalPages((int) ceil($productsResponse[1] / $filterProductsRequest->getPagination()->getLimit()))
            ->setTotalResults($productsResponse[1])
            ->setLimit($filterProductsRequest->getPagination()->getLimit())
            ->setData($productsResponse[0]);

        return new JsonResponse($this->serializer->serialize($jsonResponse, 'json', [
            'groups' => ['product:list'],
        ]), Response::HTTP_OK, [], true);
    }
}
