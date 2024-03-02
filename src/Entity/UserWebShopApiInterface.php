<?php

declare(strict_types=1);

namespace TomoPongrac\WebshopApiBundle\Entity;

interface UserWebShopApiInterface
{
    public function getId(): ?int;

    public function setEmail(string $email): self;

    public function setPassword(string $email): self;
}
