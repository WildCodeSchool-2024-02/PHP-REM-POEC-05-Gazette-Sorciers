<?php

namespace App\Model;

use PDO;
use App\Model\AbstractManager;

class UserManager extends AbstractManager
{
    public const TABLE = 'user';
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
        int $idPrivilege
    ) {
        $statement = $this->pdo->prepare(
            'INSERT INTO user (name, lastname, mail, password, profile_picture, created_at, id_privilege)' .
                'VALUES (:name, :lastname, :mail, :password, :profilePicture, NOW(), :idPrivilege)'
        );
        $statement->bindValue(':name', $name, PDO::PARAM_STR);
        $statement->bindValue(':lastname', $lastname, PDO::PARAM_STR);
        $statement->bindValue(':mail', $mail, PDO::PARAM_STR);
        $statement->bindValue(':password', $password, PDO::PARAM_STR);
        $statement->bindValue(':profilePicture', $profilePicture, PDO::PARAM_STR);
        $statement->bindValue(':idPrivilege', $idPrivilege, PDO::PARAM_INT);
        $statement->execute();
    }

    public function getPrivilegeIdByName(string $privilegeName): int
    {
        $statement = $this->pdo->prepare('SELECT id FROM privilege WHERE name = :name');
        $statement->bindValue(':name', $privilegeName, PDO::PARAM_STR);
        $statement->execute();

        $privilege = $statement->fetch(PDO::FETCH_ASSOC);
        return $privilege['id'] ?? 0;
    }

    public function getAllUsers(): array
    {
        $statement = $this->pdo->query('SELECT * FROM user');
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserById(int $id): ?array
    {
        $statement = $this->pdo->prepare('SELECT * FROM user WHERE id = :id');
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();

        $user = $statement->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    public function deleteUserById(int $id): void
    {
        $statement = $this->pdo->prepare('DELETE FROM user WHERE id = :id');
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();
    }
}
