<?php

declare(strict_types=1);

namespace TomoPongrac\WebshopApiBundle\DTO;

use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class PaginationInFilterProductsRequest
{
    #[
        Groups(['filterProducts:request']),
        Assert\NotBlank(),
        Assert\NotNull()
    ]
    private int $page;

    #[
        Groups(['filterProducts:request']),
        Assert\NotBlank(),
        Assert\NotNull()
    ]
    private int $limit;

    public function getPage(): int
    {
        return $this->page;
    }

    public function setPage(int $page): static
    {
        $this->page = $page;

        return $this;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function setLimit(int $limit): static
    {
        $this->limit = $limit;

        return $this;
    }
}
