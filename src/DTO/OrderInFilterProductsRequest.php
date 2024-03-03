<?php

declare(strict_types=1);

namespace TomoPongrac\WebshopApiBundle\DTO;

use Symfony\Component\Serializer\Attribute\Groups;

class OrderInFilterProductsRequest
{
    #[
        Groups(['filterProducts:request'])
    ]
    private ?string $by = null;

    #[
        Groups(['filterProducts:request'])
    ]
    private ?string $direction = null;

    public function getBy(): ?string
    {
        return $this->by;
    }

    public function setBy(?string $by): static
    {
        $this->by = $by;

        return $this;
    }

    public function getDirection(): ?string
    {
        return $this->direction;
    }

    public function setDirection(?string $direction): static
    {
        $this->direction = $direction;

        return $this;
    }
}
