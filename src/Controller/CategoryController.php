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
        $categorieManager = new CategoryManager();
        $categories = $categorieManager->selectAll('name');

        return $this->twig->render('Categories/index.html.twig', ['categories' => $categories]);
    }

    /**
     * Show informations for a specific categorie
     */
    public function show(int $id): string
    {
        $categorieManager = new CategoryManager();
        $categorie = $categorieManager->selectOneById($id);

        return $this->twig->render('Categories/show.html.twig', ['categorie' => $categorie]);
    }

    /**
     * Edit a specific categorie
     */
    public function edit(int $id): ?string
    {
        $categorieManager = new CategoryManager();
        $categorie = $categorieManager->selectOneById($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // clean $_POST data
            $categorie = array_map('trim', $_POST);

            // TODO validations (length, format...)
            // if validation is ok, update and redirection
            if ($this->validate(true, $categorie)) {
                $categorie['created_at'] = (new DateTime())->format('Y-m-d H:i:s');
                $categorieManager->update($categorie);

                header('Location: /Categories/show?id=' . $id);
                // we are redirecting so we don't want any content rendered
                return null;
            }
        }

        return $this->twig->render('Categories/edit.html.twig', [
            'categorie' => $categorie,
        ]);
    }

    /**
     * Add a new categorie
     */
    public function add(): ?string
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // clean $_POST data
            $categorie = array_map('trim', $_POST);
            var_dump($categorie);
            // TODO validations (length, format...)

            // if validation is ok, insert and redirection
            if ($this->validate(false, $categorie)) {
                $categorie['created_at'] = (new DateTime())->format('Y-m-d H:i:s');
                $categorieManager = new CategoryManager();
                $id = $categorieManager->insert($categorie);

                header('Location:/Categories/show?id=' . $id);
                return null;
            }
        }

        return $this->twig->render('Categories/add.html.twig');
    }

    /**
     * Delete a specific categorie
     */
    public function delete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = trim($_POST['id']);
            $categorieManager = new CategoryManager();
            $categorieManager->delete((int)$id);

            header('Location:/Categories');
        }
    }

    //DRY way to validate data in controller
    private function validate(bool $edit, array $categorie): bool
    {
        if ($categorie['name'] === '' || empty($categorie['name'])) {
            return false;
        }

        if ($categorie['description'] === '' || empty($categorie['description'])) {
            return false;
        }

        //check if id is set and is a valid integer only if we are in edit mode
        if ($edit) {
            if (!isset($categorie['id'])  || !filter_var($categorie['id'], FILTER_VALIDATE_INT)) {
                return false;
            }
        }

        return true;
    }
}
