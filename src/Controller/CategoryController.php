<?php

namespace App\Controller;

use App\Model\CategoryManager;
use App\Controller\AbstractController;
use DateTime;

class CategoryController extends AbstractController
{
    /**
     * List categories
     */
    public function index(): string
    {
        $categoryManager = new CategoryManager();
        $categories = $categoryManager->selectAll('name');

        return $this->twig->render('categories/index.html.twig', ['categories' => $categories]);
    }

    /**
     * Show informations for a specific category
     */
    public function show(int $id): string
    {
        $categoryManager = new CategoryManager();
        $category = $categoryManager->selectOneById($id);

        return $this->twig->render('categories/show.html.twig', ['category' => $category]);
    }

    /**
     * Edit a specific category
     */
    public function edit(int $id): ?string
    {
        $categoryManager = new CategoryManager();
        $category = $categoryManager->selectOneById($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // clean $_POST data
            $category = array_map('trim', $_POST);

            // TODO validations (length, format...)
            // if validation is ok, update and redirection
            if ($this->validate(true, $category)) {
                $category['created_at'] = (new DateTime())->format('Y-m-d H:i:s');
                $categoryManager->update($category);

                header('Location: /categories/show?id=' . $id);
                // we are redirecting so we don't want any content rendered
                return null;
            }
        }

        return $this->twig->render('categories/edit.html.twig', [
            'category' => $category,
        ]);
    }

    /**
     * Add a new category
     */
    public function add(): ?string
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // clean $_POST data
            $category = array_map('trim', $_POST);
            // TODO validations (length, format...)

            // if validation is ok, insert and redirection
            if ($this->validate(false, $category)) {
                $category['created_at'] = (new DateTime())->format('Y-m-d H:i:s');
                $categoryManager = new CategoryManager();
                $id = $categoryManager->insert($category);

                header('Location: /categories/show?id=' . $id);
                return null;
            }
        }

        return $this->twig->render('categories/add.html.twig');
    }

    /**
     * Delete a specific category
     */
    public function delete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = trim($_POST['id']);
            $categoryManager = new CategoryManager();
            $categoryManager->delete((int)$id);

            header('Location: /categories');
        }
    }

    //DRY way to validate data in controller
    private function validate(bool $edit, array $category): bool
    {
        if ($category['name'] === '' || empty($category['name'])) {
            return false;
        }

        if ($category['description'] === '' || empty($category['description'])) {
            return false;
        }

        //check if id is set and is a valid integer only if we are in edit mode
        if ($edit) {
            if (!isset($category['id'])  || !filter_var($category['id'], FILTER_VALIDATE_INT)) {
                return false;
            }
        }

        return true;
    }
}
