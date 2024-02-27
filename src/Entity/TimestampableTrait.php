<?php

declare(strict_types=1);

namespace TomoPongrac\WebshopApiBundle\Entity;

trait TimestampableTrait
{
    private \DateTimeInterface $createdAt;

    private ?\DateTimeInterface $updatedAt = null;

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function onCreate(): void
    {
        $this->createdAt = new \DateTime('now');
    }

    public function onUpdate(): void
    {
        $this->updatedAt = new \DateTime('now');
    }
}
