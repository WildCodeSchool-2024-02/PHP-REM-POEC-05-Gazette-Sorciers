<?php

namespace App\Model;

class ContactManager extends AbstractManager
{
    public const TABLE = 'contact';

    public function saveContact(string $name, string $email, string $message): void
    {
        $query = "INSERT INTO " . self::TABLE . " (name, email, message, created_at) 
        VALUES (:name, :email, :message, NOW())";
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':name', $name, \PDO::PARAM_STR);
        $statement->bindValue(':email', $email, \PDO::PARAM_STR);
        $statement->bindValue(':message', $message, \PDO::PARAM_STR);
        $statement->execute();
    }
}
