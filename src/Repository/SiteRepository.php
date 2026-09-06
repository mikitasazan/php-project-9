<?php

declare(strict_types=1);

namespace App\Repository;

use App\Domain\Site;
use PDO;

class SiteRepository
{
    public function __construct(
        private readonly PDO $connection,
    ) {}

    public function find(int $id): ?Site
    {
        $statement = $this->connection->prepare('SELECT * FROM urls WHERE id = :id');
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();

        return $row === false ? null : Site::fromRow($row);
    }

    public function findByName(string $name): ?Site
    {
        $statement = $this->connection->prepare('SELECT * FROM urls WHERE name = :name');
        $statement->execute(['name' => $name]);
        $row = $statement->fetch();

        return $row === false ? null : Site::fromRow($row);
    }

    /**
     * @return Site[] newest first
     */
    public function findAll(): array
    {
        $statement = $this->connection->query('SELECT * FROM urls ORDER BY id DESC');

        return $statement !== false ? array_map(Site::fromRow(...), $statement->fetchAll()) : [];
    }

    /**
     * @return array{0: Site, 1: bool} the site and whether it was just created
     */
    public function findOrCreate(string $name): array
    {
        $existing = $this->findByName($name);

        return $existing !== null ? [$existing, false] : [$this->create($name), true];
    }

    public function create(string $name): Site
    {
        $createdAt = date('Y-m-d H:i:s');
        $statement = $this->connection->prepare(
            'INSERT INTO urls (name, created_at) VALUES (:name, :created_at) RETURNING id',
        );
        $statement->execute(['name' => $name, 'created_at' => $createdAt]);
        $id = (int) $statement->fetchColumn();

        return (new Site($name, $createdAt))->withId($id);
    }
}
