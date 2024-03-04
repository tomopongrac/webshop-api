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
use TomoPongrac\WebshopApiBundle\DTO\CreateOrderRequest;
use TomoPongrac\WebshopApiBundle\Entity\UserWebShopApiInterface;
use TomoPongrac\WebshopApiBundle\Service\CreateOrderService;
use TomoPongrac\WebshopApiBundle\Service\ValidatorService;

class CreateOrderController extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly Security $security,
        private readonly CreateOrderService $createOrderService,
        private readonly ValidatorService $validatorService,
    ) {
    }

    #[
        Route('/orders', name: 'create_order', methods: ['POST']),
        OA\Post(
            tags: ['Order'],
            summary: 'Create an order',
        ),
        OA\RequestBody(
            required: true,
            content: new Model(type: CreateOrderRequest::class, groups: ['createOrder:request'])
        ),
        OA\Response(
            response: 201,
            description: 'Order created',
        ),
        OA\Response(
            response: 422,
            description: 'Validation error'
        )
    ]
    public function __invoke(Request $request): Response
    {
        $createOrderRequest = $this->serializer->deserialize($request->getContent(), CreateOrderRequest::class, 'json', [
            'groups' => ['createOrder:request'],
        ]);

        $this->validatorService->validate($createOrderRequest, [Constraint::DEFAULT_GROUP]);

        /** @var UserWebShopApiInterface $user */
        $user = $this->security->getUser();

        $this->createOrderService->createOrder($createOrderRequest, $user);

        return new JsonResponse($this->serializer->serialize([], 'json', [
            'groups' => ['product:read'],
        ]), Response::HTTP_CREATED, [], true);
    }
}
