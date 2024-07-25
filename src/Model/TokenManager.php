<?php

namespace App\Model;

use DateTime;
use PDO;

class TokenManager extends AbstractManager
{
    public const TABLE = 'token';

    public function getToken($id): array|false
    {
        $statement = $this->pdo->prepare("SELECT * FROM " . static::TABLE . " WHERE `id_user` = :id");
        $statement->bindValue('id', $id, PDO::PARAM_INT);
        $statement->execute();
        $token = $statement->fetch();
        return $token;
    }

    public function create($id, $key)
    {
        $statement = $this->pdo->prepare("INSERT INTO " . static::TABLE . "(`key`,`id_user`, `created_at`)
        VALUES(:key, :id_user, :created_at)");
        $statement->bindValue('key', $key, PDO::PARAM_STR);
        $statement->bindValue('id_user', $id, PDO::PARAM_INT);
        $statement->bindValue('created_at', (new DateTime())->format('Y-m-d H:i:s'), PDO::PARAM_STR);
        $statement->execute();
    }
    //override
    public function delete(int $id): void
    {
        // prepared request
        $statement = $this->pdo->prepare("DELETE FROM " . static::TABLE . " WHERE id_user=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();
    }
}
