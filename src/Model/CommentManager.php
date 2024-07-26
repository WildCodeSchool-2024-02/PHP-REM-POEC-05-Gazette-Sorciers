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
        $statement = $this->pdo->prepare("SELECT comment.id,comment.created_at,comment.content,comment.picture,
        user.name,user.id,user.lastname,user.profile_picture
        FROM " . static::TABLE . " INNER JOIN user ON comment.id_user=user.id WHERE id_topic=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }
}
