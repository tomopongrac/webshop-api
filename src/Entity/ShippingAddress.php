<?php

declare(strict_types=1);

namespace TomoPongrac\WebshopApiBundle\Entity;

class ShippingAddress
{
    use TimestampableTrait;

    private ?int $id = null;

    private string $address;

    private string $city;

    private string $zip;

    private string $country;

    private ?UserWebShopApiInterface $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getZip(): string
    {
        return $this->zip;
    }

    public function setZip(string $zip): static
    {
        $this->zip = $zip;

        return $this;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setCountry(string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getUser(): ?UserWebShopApiInterface
    {
        return $this->user;
    }

    public function setUser(?UserWebShopApiInterface $user): static
    {
        $this->user = $user;

        return $this;
    }
}
