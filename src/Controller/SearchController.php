<?php

namespace App\Controller;

use App\Model\SearchManager;

class SearchController extends AbstractController
{
    public function suggestions()
    {
        $query = $_GET['query'] ?? '';

        if (!empty($query)) {
            $searchManager = new SearchManager();
            $suggestions = $searchManager->getSuggestions($query);

            header('Content-Type: application/json');
            echo json_encode($suggestions);
            exit();
        }

        header('Content-Type: application/json');
        echo json_encode([]);
        exit();
    }
}
