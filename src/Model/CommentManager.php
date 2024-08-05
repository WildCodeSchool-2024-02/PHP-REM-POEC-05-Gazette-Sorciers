<?php

namespace App\Model;

use PDO;

class CommentManager extends AbstractManager
{
    public const TABLE = 'comment';


    /**
     * Insert new comment in database
     */
    public function insert(array $comment): int
    {
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE . " (`content`, `picture`, 
        `is_modified`, `id_user`, `id_topic`, `created_at`) 
            VALUES (:content, :picture, :is_modified, :id_user, :id_topic, :created_at)");
        $statement->bindValue('content', $comment['content'], PDO::PARAM_STR);
        $statement->bindValue('picture', $comment['picture'] ?? null, PDO::PARAM_STR);
        $statement->bindValue('is_modified', $comment['is_modified'] ?? 0, PDO::PARAM_INT);
        $statement->bindValue('id_user', $comment['id_user'], PDO::PARAM_INT);
        $statement->bindValue('id_topic', $comment['id_topic'], PDO::PARAM_INT);
        $statement->bindValue('created_at', date('Y-m-d H:i:s'), PDO::PARAM_STR);

        $statement->execute();
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Update comment in database
     */
    public function update(array $comment): bool
    {
        $statement = $this->pdo->prepare("UPDATE " . self::TABLE . "
            SET `content` = :content, `picture` = :picture, `is_modified` = :is_modified, 
            `id_user` = :id_user, `id_topic` = :id_topic
            WHERE `id` = :id");

        $statement->bindValue('id', $comment['id'], PDO::PARAM_INT);
        $statement->bindValue('content', $comment['content'], PDO::PARAM_STR);
        $statement->bindValue('picture', $comment['picture'] ?? null, PDO::PARAM_STR);
        $statement->bindValue('is_modified', $comment['is_modified'] ?? 0, PDO::PARAM_INT);
        $statement->bindValue('id_user', $comment['id_user'], PDO::PARAM_INT);
        $statement->bindValue('id_topic', $comment['id_topic'], PDO::PARAM_INT);

        return $statement->execute();
    }

    /**
     * Get all row from topics
     */
    public function selectAllByTopic(int $id): array
    {
        $statement = $this->pdo->prepare("SELECT * FROM " . static::TABLE . " WHERE id_topic=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function getLastComments($limit = 5)
    {
        $sql = "
        SELECT c.content AS comment_content, c.created_at AS comment_date, 
               u.name AS user_name, u.lastname AS user_lastname, 
               t.title AS topic_title, t.id AS topic_id,
               cat.name AS category_name, cat.id AS category_id
        FROM comment c
        JOIN user u ON c.id_user = u.id
        JOIN topic t ON c.id_topic = t.id
            JOIN category cat ON t.id_category = cat.id
        ORDER BY c.created_at DESC
        LIMIT " . intval($limit);

        $statement = $this->pdo->prepare($sql);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
