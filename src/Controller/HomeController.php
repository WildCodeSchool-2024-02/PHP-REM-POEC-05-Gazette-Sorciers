<?php

namespace App\Controller;

use App\Model\CategoryManager;
use App\Model\CommentManager;

class HomeController extends AbstractController
{
    /**
     * Display home page
     */
    public function index(): string
    {
        $categoriesManager = new CategoryManager();
        $categories = $categoriesManager->selectAll('name');

        $commentManager = new CommentManager();
        $comments = $commentManager->getLastComments(5);

        return $this->twig->render('Home/index.html.twig', [
            'categories' => $categories,
            'comments' => $comments
        ]);
    }
}
