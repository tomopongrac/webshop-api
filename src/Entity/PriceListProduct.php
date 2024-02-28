<?php

declare(strict_types=1);

namespace TomoPongrac\WebshopApiBundle\Entity;

class PriceListProduct
{
    use TimestampableTrait;

    private ?int $id = null;

    private int $price;

    private PriceList $priceList;

    private Product $product;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function setPrice(int $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getPriceList(): PriceList
    {
        return $this->priceList;
    }

    public function setPriceList(PriceList $priceList): static
    {
        $this->priceList = $priceList;

        return $this;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): static
    {
        $this->product = $product;

        return $this;
    }
}
