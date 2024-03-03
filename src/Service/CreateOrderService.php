<?php

declare(strict_types=1);

namespace TomoPongrac\WebshopApiBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use TomoPongrac\WebshopApiBundle\DTO\CreateOrderRequest;
use TomoPongrac\WebshopApiBundle\Entity\Order;
use TomoPongrac\WebshopApiBundle\Entity\OrderProduct;
use TomoPongrac\WebshopApiBundle\Entity\Profile;
use TomoPongrac\WebshopApiBundle\Entity\ShippingAddress;
use TomoPongrac\WebshopApiBundle\Repository\ProductRepository;

class CreateOrderService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ProductRepository $productRepository,
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

        $productsIds = array_map(fn ($product) => $product->getProductId(), $createOrderRequest->getProducts());
        $orderProducts = $this->productRepository->findProductsByIds($productsIds);
        foreach ($orderProducts as $productFromDb) {
            $quantity = array_filter($createOrderRequest->getProducts(), fn ($product) => $product->getProductId() === $productFromDb->getId())[0]->getQuantity();

            $orderProduct = (new OrderProduct())
                ->setProduct($productFromDb)
                ->setQuantity($quantity)
                ->setPrice($productFromDb->getPrice())
                ->setTaxRate($productFromDb->getTaxCategory()->getRate())
                ->setTaxAmount((int) round($productFromDb->getTaxCategory()->getRate() * $productFromDb->getPrice()))
                ->setTotalPrice($productFromDb->getPrice() * $quantity);
            $order->addProduct($orderProduct);
        }

        $this->entityManager->persist($order);
        $this->entityManager->flush();
    }
}
