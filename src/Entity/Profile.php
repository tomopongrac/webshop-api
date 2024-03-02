<?php

declare(strict_types=1);

namespace TomoPongrac\WebshopApiBundle\Entity;

class Profile
{
    use TimestampableTrait;

    private ?int $id = null;

    private string $firstName;

    private string $lastName;

    private string $phone;

    private UserWebShopApiInterface $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getUser(): UserWebShopApiInterface
    {
        return $this->user;
    }

    public function setUser(UserWebShopApiInterface $user): static
    {
        $this->user = $user;

        return $this;
    }
}
