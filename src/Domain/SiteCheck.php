<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * The result of fetching a Site once: response code plus whatever
 * SEO-relevant tags were found in the page (any of them may be empty).
 */
class SiteCheck
{
    private ?int $id = null;

    public function __construct(
        private readonly int $siteId,
        private readonly int $statusCode,
        private readonly string $h1 = '',
        private readonly string $title = '',
        private readonly string $description = '',
        private readonly ?string $createdAt = null,
    ) {}

    public static function fromRow(array $row): self
    {
        $check = new self(
            (int) $row['url_id'],
            (int) $row['status_code'],
            (string) ($row['h1'] ?? ''),
            (string) ($row['title'] ?? ''),
            (string) ($row['description'] ?? ''),
            $row['created_at'],
        );
        $check->id = (int) $row['id'];

        return $check;
    }

    public function withId(int $id): self
    {
        $clone = clone $this;
        $clone->id = $id;

        return $clone;
    }

    /**
     * @throws \LogicException if called before the check was persisted
     */
    public function getId(): int
    {
        return $this->id ?? throw new \LogicException('SiteCheck has no id yet — it was never saved');
    }

    public function getSiteId(): int
    {
        return $this->siteId;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getH1(): string
    {
        return $this->h1;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }
}
