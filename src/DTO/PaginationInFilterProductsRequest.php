<?php

declare(strict_types=1);

namespace TomoPongrac\WebshopApiBundle\DTO;

use Symfony\Component\Serializer\Attribute\Groups;

class PaginationInFilterProductsRequest
{
    #[
        Groups(['filterProducts:request'])
    ]
    private int $page;

    #[
        Groups(['filterProducts:request'])
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
