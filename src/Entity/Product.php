<?php

declare(strict_types=1);

namespace TomoPongrac\WebshopApiBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Attribute\Groups;

class Product
{
    use TimestampableTrait;

    #[
        Groups(['product:read', 'product:list'])
    ]
    private ?int $id = null;

    #[
        Groups(['product:read', 'product:list'])
    ]
    private string $name;

    private string $description;

    private int $price;

    private TaxCategory $taxCategory;

    private string $sku;

    /** @var Collection<int, Category> */
    #[
        Groups(['product:read', 'product:list'])
    ]
    private Collection $categories;

    private ?\DateTimeInterface $publishedAt = null;

    /** @var Collection<int, PriceListProduct> */
    private Collection $priceListProducts;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->priceListProducts = new ArrayCollection();
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

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function setPrice(int $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getTaxCategory(): TaxCategory
    {
        return $this->taxCategory;
    }

    public function setTaxCategory(TaxCategory $taxCategory): static
    {
        $this->taxCategory = $taxCategory;

        return $this;
    }

    public function getSku(): string
    {
        return $this->sku;
    }

    public function setSku(string $sku): static
    {
        $this->sku = $sku;

        return $this;
    }

    /** @return Collection<int, Category> */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    public function removeCategory(Category $category): static
    {
        $this->categories->removeElement($category);

        return $this;
    }

    public function getPublishedAt(): ?\DateTimeInterface
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTimeInterface $publishedAt): void
    {
        $this->publishedAt = $publishedAt;
    }

    /** @return Collection<int, PriceListProduct> */
    public function getPriceListProducts(): Collection
    {
        return $this->priceListProducts;
    }

    public function addPriceListProduct(PriceListProduct $priceListProduct): static
    {
        if (!$this->priceListProducts->contains($priceListProduct)) {
            $this->priceListProducts->add($priceListProduct);
        }

        return $this;
    }

    public function removePriceListProduct(PriceListProduct $priceListProduct): static
    {
        $this->priceListProducts->removeElement($priceListProduct);

        return $this;
    }
}
