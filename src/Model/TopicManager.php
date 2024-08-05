<?php

namespace App\Model;

use PDO;

class TopicManager extends AbstractManager
{
    public const TABLE = 'topic';

    /**
     * Insert new item in database
     */
    public function insert(array $topic): int
    {
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE .
            "(`title` ,`created_at`, `content`, `picture`, `id_category`, `id_user`)
        VALUES (:title, :created_at, :content, :picture, :id_category, :id_user)");
        $statement->bindValue('title', $topic['title'], PDO::PARAM_STR);
        $statement->bindValue('created_at', $topic['created_at'], PDO::PARAM_STR);
        $statement->bindValue('content', $topic['content'], PDO::PARAM_STR);
        $statement->bindValue('picture', $topic['picture'], PDO::PARAM_STR);
        $statement->bindValue('id_category', $topic['id_category'], PDO::PARAM_STR);
        $statement->bindValue('id_user', $topic['id_user'], PDO::PARAM_STR);
        $statement->execute();
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Get all row from one category.
     */
    public function selectAllByCategory(int $categoryId): array
    {
        $statement = $this->pdo->prepare("SELECT * FROM " . self::TABLE . " WHERE id_category = :categoryId");
        $statement->bindValue(':categoryId', $categoryId, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteByCategory(int $categoryId): void
    {
        $statement = $this->pdo->prepare("DELETE FROM " . self::TABLE . " WHERE id_category = :categoryId");
        $statement->bindValue('categoryId', $categoryId, PDO::PARAM_INT);
        $statement->execute();
    }
}
