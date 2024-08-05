<?php

namespace App\Controller;

use App\Model\CategoryManager;
use App\Model\PrivilegeManager;
use App\Model\UserManager;
use App\Controller\AbstractController;
use App\Model\TopicManager;
use App\Model\CommentManager;
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
    public function show(int $id): ?string
    {
        $categoryManager = new CategoryManager();
        $category = $categoryManager->selectOneById($id);

        return $this->twig->render('categories/show.html.twig', ['category' => $category]);
    }

    /**
     * Edit a specific category
     */
    private $categoryModel;

    public function edit()
    {
        $this->categoryModel = new CategoryManager();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? 0;
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';

            if ($id && $name && $description) {
                $this->categoryModel->update($id, $name, $description);

                header('Location: /categories/manage');
                exit();
            }
        }

        $categoryId = $_GET['id'] ?? 0;
        $category = $this->categoryModel->selectOneById($categoryId);

        if (!$category) {
            header('Location: /categories/manage');
            exit();
        }

        return $this->twig->render('categories/edit.html.twig', ['category' => $category]);
    }

    /**
     * Add a new category
     */
    public function add(): ?string
    {
        $this->checkAdminPrivilege();


        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $category = array_map('trim', $_POST);

            if ($this->validate(false, $category)) {
                $category['created_at'] = (new DateTime())->format('Y-m-d H:i:s');
                $categoryManager = new CategoryManager();
                $categoryManager->insert($category);

                header('Location: /');
                return null;
            }
        }

        return $this->twig->render('categories/add.html.twig');
    }


    public function manage(): string
    {
        $this->checkAdminPrivilege();

        $categoryManager = new CategoryManager();
        $categories = $categoryManager->selectAll('name');

        return $this->twig->render('Categories/manage.html.twig', ['categories' => $categories]);
    }
    public function delete(): void
    {
        $this->checkAdminPrivilege();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = isset($_POST['id']) ? (int)trim($_POST['id']) : 0;

            if ($id > 0) {
                $topicManager = new TopicManager();
                $commentManager = new CommentManager();

                $topics = $topicManager->selectAllByCategory($id);
                foreach ($topics as $topic) {
                    $commentManager->deleteByTopic($topic['id']);
                }

                $topicManager->deleteByCategory($id);

                $categoryManager = new CategoryManager();
                $categoryManager->delete($id);

                header('Location: /categories/manage?deleted=true');
                exit();
            }
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
