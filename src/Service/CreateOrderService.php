<?php

declare(strict_types=1);

namespace TomoPongrac\WebshopApiBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use TomoPongrac\WebshopApiBundle\DTO\CreateOrderRequest;
use TomoPongrac\WebshopApiBundle\Entity\Order;
use TomoPongrac\WebshopApiBundle\Entity\Profile;
use TomoPongrac\WebshopApiBundle\Entity\ShippingAddress;

class CreateOrderService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function createOrder(CreateOrderRequest $createOrderRequest): void
    {
        $profile = (new Profile())
            ->setFirstName($createOrderRequest->getFirstName())
            ->setLastName($createOrderRequest->getLastName())
            ->setPhone($createOrderRequest->getPhone());

        $shippingAddress = (new ShippingAddress())
            ->setAddress($createOrderRequest->getAddress())
            ->setCity($createOrderRequest->getCity())
            ->setZip($createOrderRequest->getZip())
            ->setCountry($createOrderRequest->getCountry());

        $order = (new Order())
            ->setProfile($profile)
            ->setShippingAddress($shippingAddress)
            ->setTotalPrice(100);

        $this->entityManager->persist($order);
        $this->entityManager->flush();
    }
}
