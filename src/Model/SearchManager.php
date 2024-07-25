<?php

namespace App\Model;

use PDO;

class SearchManager extends AbstractManager
{
    public function getSuggestions(string $query): array
    {
        $results = [];

        $statement = $this->pdo->prepare(
            'SELECT id, name, description, "category" as type FROM category 
            WHERE name LIKE :query OR description LIKE :query LIMIT 10'
        );
        $statement->bindValue(':query', '%' . $query . '%', PDO::PARAM_STR);
        $statement->execute();
        $results = array_merge($results, $statement->fetchAll(PDO::FETCH_ASSOC));

        $statement = $this->pdo->prepare(
            'SELECT id, title as name, content, "topic" as type FROM topic 
            WHERE title LIKE :query OR content LIKE :query LIMIT 10'
        );
        $statement->bindValue(':query', '%' . $query . '%', PDO::PARAM_STR);
        $statement->execute();
        $results = array_merge($results, $statement->fetchAll(PDO::FETCH_ASSOC));

        return $results;
    }
}
