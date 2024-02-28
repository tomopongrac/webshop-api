<?php

declare(strict_types=1);

namespace TomoPongrac\WebshopApiBundle\Entity;

class PriceList
{
    use TimestampableTrait;

    private ?int $id = null;

    private string $name;

    private ?string $description = null;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }
}
