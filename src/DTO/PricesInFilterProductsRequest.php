<?php

declare(strict_types=1);

namespace TomoPongrac\WebshopApiBundle\DTO;

use Symfony\Component\Serializer\Attribute\Groups;

class PricesInFilterProductsRequest
{
    #[
        Groups(['filterProducts:request'])
    ]
    private ?int $min = null;

    #[
        Groups(['filterProducts:request'])
    ]
    private ?int $max = null;

    public function getMin(): ?int
    {
        return $this->min;
    }

    public function setMin(?int $min): static
    {
        $this->min = $min;

        return $this;
    }

    public function getMax(): ?int
    {
        return $this->max;
    }

    public function setMax(?int $max): static
    {
        $this->max = $max;

        return $this;
    }
}
