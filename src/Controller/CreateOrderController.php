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
use TomoPongrac\WebshopApiBundle\DTO\CreateOrderRequest;
use TomoPongrac\WebshopApiBundle\Entity\UserWebShopApiInterface;
use TomoPongrac\WebshopApiBundle\Service\CreateOrderService;

class CreateOrderController extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly Security $security,
        private readonly CreateOrderService $createOrderService,
    ) {
    }

    #[Route('/orders', name: 'create_order', methods: ['POST'])]
    public function __invoke(Request $request): Response
    {
        $createOrderRequest = $this->serializer->deserialize($request->getContent(), CreateOrderRequest::class, 'json', [
            'groups' => ['createOrder:request'],
        ]);

        /** @var UserWebShopApiInterface $user */
        $user = $this->security->getUser();

        $this->createOrderService->createOrder($createOrderRequest);

        return new JsonResponse($this->serializer->serialize([], 'json', [
            'groups' => ['product:read'],
        ]), Response::HTTP_CREATED, [], true);
    }
}
