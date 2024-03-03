<?php

declare(strict_types=1);

namespace TomoPongrac\WebshopApiBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use TomoPongrac\WebshopApiBundle\DTO\CreateOrderRequest;
use TomoPongrac\WebshopApiBundle\Entity\Order;
use TomoPongrac\WebshopApiBundle\Entity\OrderProduct;
use TomoPongrac\WebshopApiBundle\Entity\Profile;
use TomoPongrac\WebshopApiBundle\Entity\ShippingAddress;
use TomoPongrac\WebshopApiBundle\Entity\UserWebShopApiInterface;
use TomoPongrac\WebshopApiBundle\Repository\ProductRepository;

class CreateOrderService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ProductRepository $productRepository,
    ) {
    }

    public function createOrder(CreateOrderRequest $createOrderRequest, ?UserWebShopApiInterface $user = null): void
    {
        $profile = (new Profile())
            ->setEmail($createOrderRequest->getEmail())
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
            ->setShippingAddress($shippingAddress);

        $productsIds = array_map(fn ($product) => $product->getProductId(), $createOrderRequest->getProducts());
        $productsFromDb = $this->productRepository->findProductsByIds($productsIds, $user);

        $orderTotalPrice = 0;
        foreach ($productsFromDb as $productFromDb) {
            $createOrderProductFromRequest = array_filter($createOrderRequest->getProducts(), fn ($product) => $product->getProductId() === $productFromDb->getId());

            if (0 === count($createOrderProductFromRequest)) {
                continue;
            }

            $quantity = array_pop($createOrderProductFromRequest)?->getQuantity();

            $productPrice = $productFromDb->getPrice() * $quantity;
            $taxAmount = (int) round($productFromDb->getTaxCategory()->getRate() * $productPrice);
            $totalPrice = $productPrice + $taxAmount;
            $orderProduct = (new OrderProduct())
                ->setProduct($productFromDb)
                ->setQuantity($quantity)
                ->setPrice($productPrice)
                ->setTaxRate($productFromDb->getTaxCategory()->getRate())
                ->setTaxAmount($taxAmount)
                ->setTotalPrice($totalPrice);

            $order->addProduct($orderProduct);
            $orderTotalPrice += $totalPrice;
        }

        $order->setTotalPrice($orderTotalPrice);

        $this->entityManager->persist($order);
        $this->entityManager->flush();
    }
}
