<?php

namespace App\Controller;

use App\Model\CategoryManager;

class HomeController extends AbstractController
{
    /**
     * Display home page
     */
    public function index(): string
    {
        $categoriesManager = new CategoryManager();
        $categories = $categoriesManager->selectAll('name');
        return $this->twig->render('Home/index.html.twig', ['categories' => $categories]);
    }
}
