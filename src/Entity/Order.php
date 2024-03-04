<?php

declare(strict_types=1);

namespace TomoPongrac\WebshopApiBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Order
{
    use TimestampableTrait;

    private ?int $id = null;

    private ShippingAddress $shippingAddress;

    private Profile $profile;

    private ?UserWebShopApiInterface $user = null;

    private int $totalPrice;

    /** @var Collection<int, OrderProduct> */
    private Collection $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getShippingAddress(): ShippingAddress
    {
        return $this->shippingAddress;
    }

    public function setShippingAddress(ShippingAddress $shippingAddress): static
    {
        $this->shippingAddress = $shippingAddress;

        return $this;
    }

    public function getProfile(): Profile
    {
        return $this->profile;
    }

    public function setProfile(Profile $profile): static
    {
        $this->profile = $profile;

        return $this;
    }

    public function getUser(): ?UserWebShopApiInterface
    {
        return $this->user;
    }

    public function setUser(?UserWebShopApiInterface $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getTotalPrice(): int
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(int $totalPrice): static
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    /** @return Collection<int, OrderProduct> */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(OrderProduct $product): static
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->setOrder($this);
        }

        return $this;
    }
}
