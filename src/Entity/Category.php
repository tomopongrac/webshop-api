<?php

declare(strict_types=1);

namespace TomoPongrac\WebshopApiBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Category
{
    private ?int $id = null;

    private string $name;

    private ?string $description = null;

    private ?Category $parent = null;

    /** @var Collection<int, Category> */
    private Collection $children;

    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

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

    public function getParent(): ?Category
    {
        return $this->parent;
    }

    public function setParent(?Category $parent): static
    {
        $this->parent = $parent;

        return $this;
    }

    /** @return Collection<int, Category> */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    /** @param Collection<int, Category> $children */
    public function setChildren(Collection $children): static
    {
        $this->children = $children;

        return $this;
    }
}
