<?php

declare(strict_types=1);

namespace TomoPongrac\WebshopApiBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use TomoPongrac\WebshopApiBundle\DTO\CreateOrderRequest;
use TomoPongrac\WebshopApiBundle\Entity\Order;
use TomoPongrac\WebshopApiBundle\Entity\OrderProduct;
use TomoPongrac\WebshopApiBundle\Entity\Profile;
use TomoPongrac\WebshopApiBundle\Entity\ShippingAddress;
use TomoPongrac\WebshopApiBundle\Entity\TotalDiscount;
use TomoPongrac\WebshopApiBundle\Entity\UserWebShopApiInterface;
use TomoPongrac\WebshopApiBundle\Repository\ProductRepository;
use TomoPongrac\WebshopApiBundle\Repository\TotalDiscountRepository;

class CreateOrderService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ProductRepository $productRepository,
        private readonly TotalDiscountRepository $totalDiscountRepository,
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
            ->setCountry($createOrderRequest->getCountry())
            ->setUser($user);

        $order = (new Order())
            ->setProfile($profile)
            ->setShippingAddress($shippingAddress);

        $productsIds = array_map(fn ($product) => $product->getProductId(), $createOrderRequest->getProducts());
        $productsFromDb = $this->productRepository->findProductsByIds($productsIds, $user);

        // Calculate total price
        $orderTotalPrice = $this->calculateTotalPrice(
            $productsFromDb,
            $createOrderRequest,
            $order,
            true
        );

        // Check if there is a total discount
        $totalDiscount = $this->totalDiscountRepository->findTotalDiscount($orderTotalPrice);

        $orderTotalPrice = $this->calculateTotalPrice(
            $productsFromDb,
            $createOrderRequest,
            $order,
            false,
            $totalDiscount
        );

        $order->setTotalPrice($orderTotalPrice);
        $order->setUser($user);

        $this->entityManager->persist($order);
        $this->entityManager->flush();
    }

    protected function calculateTotalPrice(
        array $productsFromDb,
        CreateOrderRequest $createOrderRequest,
        Order $order,
        bool $onlyTotalPrice = false,
        ?TotalDiscount $totalDiscount = null
    ): int {
        $orderTotalPrice = 0;

        foreach ($productsFromDb as $productFromDb) {
            $createOrderProductFromRequest = array_filter(
                $createOrderRequest->getProducts(),
                fn ($product) => $product->getProductId() === $productFromDb->getId()
            );

            if (0 === count($createOrderProductFromRequest)) {
                continue;
            }

            $quantity = array_pop($createOrderProductFromRequest)?->getQuantity();

            $productPrice = (int) round($productFromDb->getPrice() * $quantity * (1 - ($totalDiscount?->getDiscountRate() ?? 0)));
            $taxAmount = (int) round($productFromDb->getTaxCategory()->getRate() * $productPrice);
            $totalPrice = $productPrice + $taxAmount;

            if ($onlyTotalPrice) {
                $orderTotalPrice += $totalPrice;
                continue;
            }

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

        return $orderTotalPrice;
    }
}
