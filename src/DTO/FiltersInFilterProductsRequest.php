<?php

declare(strict_types=1);

namespace TomoPongrac\WebshopApiBundle\DTO;

use Symfony\Component\Serializer\Attribute\Groups;

class FiltersInFilterProductsRequest
{
    #[
        Groups(['filterProducts:request'])
    ]
    private ?string $name = null;

    #[
        Groups(['filterProducts:request'])
    ]
    private ?int $price = null;

    #[
        Groups(['filterProducts:request'])
    ]
    private array $categories;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(?int $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getCategories(): array
    {
        return $this->categories;
    }

    public function setCategories(array $categories): static
    {
        $this->categories = $categories;

        return $this;
    }
}
