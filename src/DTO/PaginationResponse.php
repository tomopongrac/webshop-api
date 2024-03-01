<?php

declare(strict_types=1);

namespace TomoPongrac\WebshopApiBundle\DTO;

use Symfony\Component\Serializer\Attribute\Groups;
use TomoPongrac\WebshopApiBundle\Entity\EntityPaginationInterface;

final class PaginationResponse
{
    #[Groups(['product:list'])]
    private int $currentPage;

    #[Groups(['product:list'])]
    private int $totalPages;

    #[Groups(['product:list'])]
    private int $totalResults;

    #[Groups(['product:list'])]
    private int $limit;

    #[Groups(['product:list'])]
    /** @var array<EntityPaginationInterface> */
    private array $data;

    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    public function setCurrentPage(int $currentPage): static
    {
        $this->currentPage = $currentPage;

        return $this;
    }

    public function getTotalPages(): int
    {
        return $this->totalPages;
    }

    public function setTotalPages(int $totalPages): static
    {
        $this->totalPages = $totalPages;

        return $this;
    }

    public function getTotalResults(): int
    {
        return $this->totalResults;
    }

    public function setTotalResults(int $totalResults): static
    {
        $this->totalResults = $totalResults;

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

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }
}
