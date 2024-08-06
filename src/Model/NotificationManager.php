<?php

namespace App\Model;

use DateTime;
use PDO;

class NotificationManager extends AbstractManager
{
    public const TABLE = 'notification';

    public function getNotifications(int $id): array
    {
        $statement = $this->pdo->prepare("SELECT * FROM " . static::TABLE . " WHERE id_user=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }
    public function insert(int $idUser, int $idTopic, string $date): int
    {
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE .
            "(`id_user` ,`id_topic`, `created_at`)
        VALUES (:id_user,  :id_topic, :created_at)");
        $statement->bindValue('id_user', $idUser, PDO::PARAM_INT);
        $statement->bindValue('id_topic', $idTopic, PDO::PARAM_INT);
        $statement->bindValue('created_at', $date, PDO::PARAM_STR);
        $statement->execute();
        return (int)$this->pdo->lastInsertId();
    }
}
