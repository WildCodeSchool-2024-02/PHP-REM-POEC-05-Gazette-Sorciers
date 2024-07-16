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
        int $idPrivilege
    ) {
        $statement = $this->pdo->prepare(
            'INSERT INTO user (name, lastname, mail, password, profile_picture, created_at, id_privilleges)' .
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
        $statement = $this->pdo->prepare('SELECT id FROM privilleges WHERE name = :name');
        $statement->bindValue(':name', $privilegeName, PDO::PARAM_STR);
        $statement->execute();

        $privilege = $statement->fetch(PDO::FETCH_ASSOC);
        return $privilege['id'] ?? 0;
    }

    public function getUserById($id)
    {
        $statement = $this->pdo->prepare("SELECT name, lastname, password, profile_picture FROM user WHERE id = :id");
        $statement->bindValue('id', $id, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetch();
    }

    public function getUserLastComment($id, $limit = 3)
    {
        $sql = "SELECT content FROM comment WHERE id = ? ORDER BY created_at DESC LIMIT " . intval($limit);
        $statement = $this->pdo->prepare($sql);
        $statement->execute([$id]);
        return $statement->fetchAll();
    }
}
