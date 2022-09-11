<?php

declare(strict_types=1);


namespace App\Model;


use PDO;

class Post extends Model
{
    protected string $table = 'post';

    public function index(): array
    {
        $sql = 'SELECT * FROM post';

        $stmt = $this->connection->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function byId(int $id): array
    {
        $sql = 'SELECT * FROM post  WHERE id = :id';
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([':id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}