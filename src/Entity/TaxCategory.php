<?php

declare(strict_types=1);

namespace TomoPongrac\WebshopApiBundle\Entity;

class TaxCategory
{
    use TimestampableTrait;

    private ?int $id = null;

    private string $name;

    private float $rate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getRate(): float
    {
        return $this->rate;
    }

    public function setRate(float $rate): static
    {
        $this->rate = $rate;

        return $this;
    }
}
