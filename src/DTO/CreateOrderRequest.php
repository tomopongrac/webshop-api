<?php

declare(strict_types=1);

namespace TomoPongrac\WebshopApiBundle\DTO;

use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use TomoPongrac\WebshopApiBundle\Validator\OrderProductQuantityIsGreaterThatZero;
use TomoPongrac\WebshopApiBundle\Validator\ProductExists;

class CreateOrderRequest
{
    #[
        Groups(['createOrder:request']),
        Assert\NotBlank(),
        Assert\Email()
    ]
    private string $email;

    #[
        Groups(['createOrder:request']),
        Assert\NotBlank()
    ]
    private string $firstName;

    #[
        Groups(['createOrder:request']),
        Assert\NotBlank()
    ]
    private string $lastName;

    #[
        Groups(['createOrder:request']),
        Assert\NotBlank()
    ]
    private string $phone;

    #[
        Groups(['createOrder:request']),
        Assert\NotBlank()
    ]
    private string $address;

    #[
        Groups(['createOrder:request']),
        Assert\NotBlank()
    ]
    private string $city;

    #[
        Groups(['createOrder:request']),
        Assert\NotBlank()
    ]
    private string $zip;

    #[
        Groups(['createOrder:request']),
        Assert\NotBlank()
    ]
    private string $country;

    #[
        Groups(['createOrder:request']),
        Assert\NotBlank(),
        ProductExists(),
        OrderProductQuantityIsGreaterThatZero()
    ]
    /** @var ProductInOrderRequest[] */
    private array $products;

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    public function getZip(): string
    {
        return $this->zip;
    }

    public function setZip(string $zip): void
    {
        $this->zip = $zip;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setCountry(string $country): void
    {
        $this->country = $country;
    }

    public function getProducts(): array
    {
        return $this->products;
    }

    public function setProducts(array $products): void
    {
        $this->products = $products;
    }
}
