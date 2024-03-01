<?php

declare(strict_types=1);

namespace TomoPongrac\WebshopApiBundle\Entity;

class ContractListProduct
{
    use TimestampableTrait;

    private ?int $id = null;

    private int $price;

    private UserWebShopApiInterface $user;

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

    public function getUser(): UserWebShopApiInterface
    {
        return $this->user;
    }

    public function setUser(UserWebShopApiInterface $user): static
    {
        $this->user = $user;

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
