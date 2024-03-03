<?php

declare(strict_types=1);

namespace TomoPongrac\WebshopApiBundle\Service;

use TomoPongrac\WebshopApiBundle\Entity\UserWebShopApiInterface;
use TomoPongrac\WebshopApiBundle\Repository\ContractListProductRepository;

class ModifyPricesForProductsService
{
    public function __construct(
        private ContractListProductRepository $contractListProductRepository
    ) {
    }

    public function modifyPricesForProducts(array $products, ?UserWebShopApiInterface $user = null): void
    {
        if (0 === count($products)) {
            return;
        }

        foreach ($products as $product) {
            // Retrieve all prices from priceListProducts
            $prices = array_map(function ($priceListProduct) {
                return $priceListProduct->getPrice();
            }, $product->getPriceListProducts()->toArray());

            // check if there is a contract price for the user
            if (null !== $user) {
                $contractPrice = $this->contractListProductRepository->findContractPriceListForUserAndProduct(
                    $user,
                    $product
                );
                if (null !== $contractPrice) {
                    $prices[] = $contractPrice->getPrice();
                }
            }

            // Check if there are any prices before calculating the minimum
            if (count($prices) > 0) {
                $min_price = min($prices);
                $product->setPrice($min_price);
            }
        }

        return;
    }
}
