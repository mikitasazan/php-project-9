<?php

declare(strict_types=1);

namespace App\Repository;

use App\Domain\SiteCheck;
use PDO;

class SiteCheckRepository
{
    public function __construct(
        private readonly PDO $connection,
    ) {}

    public function create(SiteCheck $check): SiteCheck
    {
        $createdAt = date('Y-m-d H:i:s');
        $statement = $this->connection->prepare('INSERT INTO url_checks (url_id, status_code, h1, title, description, created_at)
             VALUES (:url_id, :status_code, :h1, :title, :description, :created_at)
             RETURNING id');
        $statement->execute([
            'url_id' => $check->getSiteId(),
            'status_code' => $check->getStatusCode(),
            'h1' => $check->getH1(),
            'title' => $check->getTitle(),
            'description' => $check->getDescription(),
            'created_at' => $createdAt,
        ]);
        $id = (int) $statement->fetchColumn();

        return SiteCheck::fromRow([
            'id' => $id,
            'url_id' => $check->getSiteId(),
            'status_code' => $check->getStatusCode(),
            'h1' => $check->getH1(),
            'title' => $check->getTitle(),
            'description' => $check->getDescription(),
            'created_at' => $createdAt,
        ]);
    }

    /**
     * @return SiteCheck[] newest first
     */
    public function findBySiteId(int $siteId): array
    {
        $statement = $this->connection->prepare('SELECT * FROM url_checks WHERE url_id = :url_id ORDER BY id DESC');
        $statement->execute(['url_id' => $siteId]);

        return array_map(SiteCheck::fromRow(...), $statement->fetchAll());
    }

    /**
     * The latest check per site, keyed by site id — one query instead of N.
     *
     * @return array<int, SiteCheck>
     */
    public function findLatestPerSite(): array
    {
        $statement = $this->connection->query(
            'SELECT DISTINCT ON (url_id) * FROM url_checks ORDER BY url_id DESC, id DESC',
        );
        $rows = $statement !== false ? $statement->fetchAll() : [];

        $latest = [];
        foreach ($rows as $row) {
            $latest[(int) $row['url_id']] = SiteCheck::fromRow($row);
        }

        return $latest;
    }
}
