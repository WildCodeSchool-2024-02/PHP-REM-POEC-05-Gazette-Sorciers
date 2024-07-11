<?php

namespace App\Model;

use PDO;

class UserManager
{
    private PDO $pdo;

    public function __construct()
    {
        $connection = new Connection();
        $this->pdo = $connection->getConnection();
    }

    public function getUserByMail(string $mail): ?array
    {
        $statement = $this->pdo->prepare('SELECT * FROM user WHERE mail = :mail');
        $statement->bindValue(':mail', $mail, PDO::PARAM_STR);
        $statement->execute();

        $user = $statement->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    public function createUser(
        string $name,
        string $lastname,
        string $mail,
        string $password,
        ?string $profilePicture,
        int $idRole
    ) {
        $statement = $this->pdo->prepare(
            'INSERT INTO user (name, lastname, mail, password, profile_picture, created_at, id_role)' .
            'VALUES (:name, :lastname, :mail, :password, :profilePicture, NOW(), :idRole)'
        );
        $statement->bindValue(':name', $name, PDO::PARAM_STR);
        $statement->bindValue(':lastname', $lastname, PDO::PARAM_STR);
        $statement->bindValue(':mail', $mail, PDO::PARAM_STR);
        $statement->bindValue(':password', $password, PDO::PARAM_STR);
        $statement->bindValue(':profilePicture', $profilePicture, PDO::PARAM_STR);
        $statement->bindValue(':idRole', $idRole, PDO::PARAM_INT);
        $statement->execute();
    }

    public function getRoleIdByName(string $roleName): int
    {
        $statement = $this->pdo->prepare('SELECT id_role FROM role WHERE name = :name');
        $statement->bindValue(':name', $roleName, PDO::PARAM_STR);
        $statement->execute();

        $role = $statement->fetch(PDO::FETCH_ASSOC);
        return $role['id_role'] ?? 0;
    }
}
