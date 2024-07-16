<?php

namespace App\Model;

use PDO;

class PrivilegeManager extends AbstractManager
{
    public const TABLE = 'privilege';

    public function isUserAdmin($id): bool
    {
        $statement = $this->pdo->prepare("SELECT * FROM " . self::TABLE . " WHERE name = 'ADMIN'");
        $statement->execute();
        $privileges = $statement->fetch();
        if (empty($privileges)) {
            return false;
        }

        return $id == $privileges['id'];
    }
}
