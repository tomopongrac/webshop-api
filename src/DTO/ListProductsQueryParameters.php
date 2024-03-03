<?php

declare(strict_types=1);

namespace TomoPongrac\WebshopApiBundle\DTO;

use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

final class ListProductsQueryParameters
{
    #[
        Groups(['product:list-query-parameters']),
        Assert\NotBlank()
    ]
    private ?string $page = null;

    #[
        Groups(['product:list-query-parameters']),
        Assert\NotBlank()
    ]
    private ?string $limit = null;

    public function getPage(): ?string
    {
        return $this->page;
    }

    public function setPage(?string $page): static
    {
        $this->page = $page;

        return $this;
    }

    public function getLimit(): ?string
    {
        return $this->limit;
    }

    public function setLimit(?string $limit): static
    {
        $this->limit = $limit;

        return $this;
    }
}
