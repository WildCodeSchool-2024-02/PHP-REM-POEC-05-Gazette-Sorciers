<?php

namespace App\Model;

use PDO;

class CategoryManager extends AbstractManager
{
    public const TABLE = 'Category';

    /**
     * Insert new item in database
     */
    public function insert(array $item): int
    {
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE .
            "(`name` ,`description`, `created_at`, `picture`)
            VALUES (:name, :description, :created_at, :picture)");
        $statement->bindValue('name', $item['name'], PDO::PARAM_STR);
        $statement->bindValue('description', $item['description'], PDO::PARAM_STR);
        $statement->bindValue('created_at', $item['created_at'], PDO::PARAM_STR);
        $statement->bindValue('picture', $item['picture'], PDO::PARAM_STR);

        $statement->execute();
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Update item in database
     */
    public function update(
        int $id,
        string $name,
        string $description,
        string $picture,
    ) {
        $sql = "UPDATE " . self::TABLE . " SET name = :name, 
        description = :description, picture = :picture WHERE id = :id";

        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':name', $name, PDO::PARAM_STR);
        $statement->bindValue(':description', $description, PDO::PARAM_STR);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->bindValue(':picture', $picture, PDO::PARAM_STR);


        $statement->execute();
    }
    public function selectOneById(int $id): array
    {
        $sql = "SELECT * FROM " . self::TABLE . " WHERE id = :id";
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        return $result;
    }
}
