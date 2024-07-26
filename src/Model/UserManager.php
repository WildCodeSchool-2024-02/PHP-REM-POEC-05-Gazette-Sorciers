<?php

namespace App\Model;

use PDO;
use App\Model\AbstractManager;

class UserManager extends AbstractManager
{
    public const TABLE = 'user';
    /**
     * Récupère un utilisateur par email
     */
    public function getUserByMail(string $mail): ?array
    {
        $statement = $this->pdo->prepare('SELECT * FROM user WHERE mail = :mail');
        $statement->bindValue(':mail', $mail, PDO::PARAM_STR);
        $statement->execute();

        $user = $statement->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    /**
     * Crée un utilisateur
     */
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

    /**
     * Récupère l'ID du privilège par son nom
     */
    public function getPrivilegeIdByName(string $privilegeName): int
    {
        $statement = $this->pdo->prepare('SELECT id FROM privilege WHERE name = :name');
        $statement->bindValue(':name', $privilegeName, PDO::PARAM_STR);
        $statement->execute();

        $privilege = $statement->fetch(PDO::FETCH_ASSOC);
        return $privilege['id'] ?? 0;
    }

    /**
     * Récupère un utilisateur par ID
     */
    public function getUserById(int $id): ?array
    {
        $statement = $this->pdo->prepare("SELECT name, lastname, password, description,
        profile_picture, mail, created_at,  id_privilege, id FROM user WHERE id = :id");
        $statement->bindValue('id', $id, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Récupère les derniers commentaires d'un utilisateur
     */
    public function getUserLastComment($userId, $limit = 3)
    {
        $sql = "
    SELECT t.title AS topic_title, c.content AS comment_content, c.created_at AS comment_date
    FROM comment c
    JOIN topic t ON c.id_topic = t.id
    WHERE c.id_user = ?
    ORDER BY c.created_at DESC
    LIMIT " . intval($limit);

        $statement = $this->pdo->prepare($sql);
        $statement->execute([$userId]);
        return $statement->fetchAll();
    }

    /**
     * Récupère tous les utilisateurs
     */
    public function getAllUsers(): array
    {
        $statement = $this->pdo->query('SELECT id, name, lastname, mail, profile_picture, created_at, ' .
            'id_privilege FROM user');
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Supprime un utilisateur par ID
     */
    public function deleteUserById(int $id): void
    {
        $statement = $this->pdo->prepare('DELETE FROM user WHERE id = :id');
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();
    }

    /**
     * Met à jour un utilisateur
     */
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
