<?php

declare(strict_types=1);

namespace TomoPongrac\WebshopApiBundle\DTO;

use Symfony\Component\Serializer\Attribute\Groups;

class FilterProductsRequest
{
    #[
        Groups(['filterProducts:request'])
    ]
    private FiltersInFilterProductsRequest $filters;

    #[
        Groups(['filterProducts:request'])
    ]
    private OrderInFilterProductsRequest $order;

    #[
        Groups(['filterProducts:request'])
    ]
    private PaginationInFilterProductsRequest $pagination;

    public function getFilters(): FiltersInFilterProductsRequest
    {
        return $this->filters;
    }

    public function setFilters(FiltersInFilterProductsRequest $filters): static
    {
        $this->filters = $filters;

        return $this;
    }

    public function getOrder(): OrderInFilterProductsRequest
    {
        return $this->order;
    }

    public function setOrder(OrderInFilterProductsRequest $order): static
    {
        $this->order = $order;

        return $this;
    }

    public function getPagination(): PaginationInFilterProductsRequest
    {
        return $this->pagination;
    }

    public function setPagination(PaginationInFilterProductsRequest $pagination): static
    {
        $this->pagination = $pagination;

        return $this;
    }
}
