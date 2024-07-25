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

    public function getUserById($id)
    {
        $statement = $this->pdo->prepare("SELECT name, lastname, password, description,
        profile_picture, mail, created_at, id FROM user WHERE id = :id");
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

    public function getAllUsers(): array
    {
        $statement = $this->pdo->query('SELECT id, name, lastname, mail, profile_picture, created_at, ' .
            'id_privilege FROM user');
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteUserById(int $id): void
    {
        $statement = $this->pdo->prepare('DELETE FROM user WHERE id = :id');
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();
    }

    public function updateUser(
        int $id,
        string $name,
        string $lastname,
        string $mail,
        string $password,
        string $description
    ) {
        $sql = "UPDATE " . self::TABLE . " SET name = :name, lastname = :lastname, 
        mail = :mail, description = :description WHERE id = :id";
        if ($password) {
            $sql = "UPDATE " . self::TABLE . " SET name = :name, lastname = :lastname, 
            mail = :mail, password = :password, description = :description WHERE id = :id";
        }

        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':name', $name, PDO::PARAM_STR);
        $statement->bindValue(':lastname', $lastname, PDO::PARAM_STR);
        $statement->bindValue(':mail', $mail, PDO::PARAM_STR);
        $statement->bindValue(':description', $description, PDO::PARAM_STR);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        if ($password) {
            $hashedPassword = password_hash($password, PASSWORD_ARGON2I, [
                'memory_cost' => 1 << 17,
                'time_cost' => 4,
                'threads' => 2
            ]);
            $statement->bindValue(':password', $hashedPassword, PDO::PARAM_STR);
        }
        $statement->execute();
    }

    public function updatePassword($id, $password)
    {
        $sql = "UPDATE " . self::TABLE . " SET password=:password WHERE id = :id";
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->bindValue(':password', $password, PDO::PARAM_STR);
        $statement->execute();
    }
}
