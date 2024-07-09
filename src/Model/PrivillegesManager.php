<?php

namespace App\Model;

use PDO;

class PrivillegesManager extends AbstractManager
{
    public const TABLE = 'privilleges';

    public function isUserAdmin($user): bool
    {
        $statement = $this->pdo->prepare("SELECT * FROM " . self::TABLE . " WHERE name = 'Admin'");
        $statement->execute();
        $privilleges = $statement->fetch();
        if (empty($privilleges) == true) {

            return false;
        }

        return (int)$user['id_privilleges'] == (int)$privilleges['id'];
    }
}
