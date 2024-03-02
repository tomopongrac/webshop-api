<?php

declare(strict_types=1);

namespace TomoPongrac\WebshopApiBundle\Entity;

class Order
{
    use TimestampableTrait;

    private ?int $id = null;

    private ShippingAddress $shippingAddress;

    private Profile $profile;

    private UserWebShopApiInterface $user;

    private int $totalPrice;

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

    public function getUser(): UserWebShopApiInterface
    {
        return $this->user;
    }

    public function setUser(UserWebShopApiInterface $user): static
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
}
