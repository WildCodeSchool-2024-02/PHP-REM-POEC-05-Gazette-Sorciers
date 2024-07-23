<?php

namespace App\Model;

use PDO;

class PrivilegeManager extends AbstractManager
{
    public const TABLE = 'privilege';

    public function isUserAdmin($id): bool
    {
        $statement = $this->pdo->prepare("SELECT id FROM " . self::TABLE . " WHERE name = :name");
        $statement->execute(['name' => 'ADMIN']);
        $privileges = $statement->fetch();
        // je vérifie si le privilège ADMIN existe et compare l'ID
        if ($privileges === false) {
            return false;
        }

        return isset($privileges['id']) && (int)$id === (int)$privileges['id'];
    }
}
