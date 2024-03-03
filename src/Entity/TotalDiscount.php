<?php

declare(strict_types=1);

namespace TomoPongrac\WebshopApiBundle\Entity;

class TotalDiscount
{
    use TimestampableTrait;

    private ?int $id = null;

    private int $totalPrice;

    private float $discountRate;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDiscountRate(): float
    {
        return $this->discountRate;
    }

    public function setDiscountRate(float $discountRate): static
    {
        $this->discountRate = $discountRate;

        return $this;
    }
}
