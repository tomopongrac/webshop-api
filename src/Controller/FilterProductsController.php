<?php

declare(strict_types=1);

namespace TomoPongrac\WebshopApiBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraint;
use TomoPongrac\WebshopApiBundle\DTO\FilterProductsRequest;
use TomoPongrac\WebshopApiBundle\DTO\PaginationResponse;
use TomoPongrac\WebshopApiBundle\Entity\UserWebShopApiInterface;
use TomoPongrac\WebshopApiBundle\Repository\ProductRepository;
use TomoPongrac\WebshopApiBundle\Service\ValidatorService;

class FilterProductsController extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly Security $security,
        private readonly ProductRepository $productRepository,
        private readonly ValidatorService $validatorService,
    ) {
    }

    #[
        Route('/products/filter', name: 'filter_products', methods: ['POST']),
        OA\Post(
            tags: ['Product'],
            summary: 'Filter products',
        ),
        OA\RequestBody(
            required: true,
            content: new Model(type: FilterProductsRequest::class, groups: ['filterProducts:request'])
        ),
        OA\Response(
            response: 200,
            description: 'Products found',
            content: new Model(type: PaginationResponse::class, groups: ['product:list'])
        ),
        OA\Response(
            response: 422,
            description: 'Validation error'
        )
    ]
    public function __invoke(Request $request): Response
    {
        /** @var FilterProductsRequest $filterProductsRequest */
        $filterProductsRequest = $this->serializer->deserialize($request->getContent(), FilterProductsRequest::class, 'json', [
            'groups' => ['filterProducts:request'],
        ]);

        $this->validatorService->validate($filterProductsRequest, [Constraint::DEFAULT_GROUP]);
        $this->validatorService->validate($filterProductsRequest->getPagination(), [Constraint::DEFAULT_GROUP]);

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
