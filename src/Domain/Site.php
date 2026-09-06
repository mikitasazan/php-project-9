<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * One tracked page: its normalized address (scheme + host, lower-cased)
 * and when it was first added.
 */
class Site
{
    private ?int $id = null;

    public function __construct(
        private readonly string $name,
        private readonly ?string $createdAt = null,
    ) {}

    public static function fromRow(array $row): self
    {
        $site = new self($row['name'], $row['created_at']);
        $site->id = (int) $row['id'];

        return $site;
    }

    public function withId(int $id): self
    {
        $clone = clone $this;
        $clone->id = $id;

        return $clone;
    }

    /**
     * @throws \LogicException if called before the site was persisted
     */
    public function getId(): int
    {
        return $this->id ?? throw new \LogicException('Site has no id yet — it was never saved');
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }
}
