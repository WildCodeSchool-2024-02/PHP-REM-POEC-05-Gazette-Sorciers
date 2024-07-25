<?php

namespace App\Controller;

use App\Model\TopicManager;
use App\Controller\AbstractController;
use App\Model\CategoryManager;
use App\Model\CommentManager;
use App\Model\PrivilegeManager;
use App\Model\UserManager;
use DateTime;

class TopicController extends AbstractController
{
    /**
     * List items
     */
    public function index(string $id): string
    {
        $topicManager = new TopicManager();
        $topics = $topicManager->selectAllByCategory($id);
        return $this->twig->render('Topic/index.html.twig', ['topics' => $topics]);
    }

    /**
     * Show informations for a specific item
     */
    public function show(int $id): string
    {
        $topicManager = new TopicManager();
        $topic = $topicManager->selectOneById($id);
        $commentManager = new CommentManager();
        $comments = $commentManager->selectAllByTopic($id);

        return $this->twig->render('Topic/show.html.twig', ['topic' => $topic, 'comments' => $comments]);
    }

    /**
     * Add a new topic
     */
    public function add(int $id): ?string
    {
        if (!$this->user) {
            header('Location: /login');
            exit();
        }

        $privilegeManager = new PrivilegeManager();
        $userManager = new UserManager();
        $user = $userManager->selectOneById($this->user['id']);
        if (!$privilegeManager->isUserAdmin($user['id_privilege'])) {
            header('Location: /');
            exit();
        }

        $categoryManager = new CategoryManager();
        $category = $categoryManager->selectOneById($id);

        $errors = [];
        $uploadDir = 'upload/';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // clean $_POST data
            $topic = array_map('trim', $_POST);
            $topicFile = array_map('trim', $_FILES['picture']);
            $errors = $this->validate($topic, $topicFile);

            if (empty($errors)) {
                if (($topicFile['size'] != '0')) {
                    $fileName = (new DateTime())->format('Y-m-d-H-i-s') . '-' . $topicFile['name'];
                    $topic['picture'] = $uploadDir . basename($fileName);
                    $uploadFile = $uploadDir . basename($fileName);
                    move_uploaded_file($_FILES['picture']['tmp_name'], $uploadFile);
                }
                $topic['created_at'] = (new DateTime())->format('Y-m-d H:i:s');
                $topic['id_category'] = $category['id'];
                $topic['id_user'] = $this->user['1'];
                $topicManager = new TopicManager();
                $id = $topicManager->insert($topic);

                header('Location: /topic/show?id=' . $id);
                return null;
            }
        }
        return $this->twig->render('topic/add.html.twig', ['errors' => $errors, 'category' => $category]);
    }

    /**
     * Delete a specific item
     */
    public function delete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = trim($_POST['id']);
            $itemManager = new TopicManager();
            $itemManager->delete((int)$id);

            header('Location:/');
        }
    }

    private function validate(array $topic, array $topicFile): array
    {
        $extension = pathinfo($topicFile['name'], PATHINFO_EXTENSION);
        $authorizedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $maxFileSize = 2000000;
        $errors = [];

        if ($topic['title'] === '' || empty($topic['title'])) {
            $errors[] = 'Votre titre est vide !';
        }

        if ($topic['content'] === '' || empty($topic['content'])) {
            $errors[] = 'Votre description est vide !';
        }

        if ((file_exists($topicFile['tmp_name']) && !in_array($extension, $authorizedExtensions))) {
            $errors[] = 'Veuillez sélectionner une image de type Jpg ou Jpeg ou Png ou Gif !';
        }

        if (file_exists($topicFile['tmp_name']) && filesize($topicFile['tmp_name']) > $maxFileSize) {
            $errors[] = "Votre fichier doit faire moins de 2Mo !";
        }

        if (file_exists($topicFile['tmp_name']) && strlen($topicFile['name']) + 20 > 100) {
            $errors[] = "Le nom de votre fichier doit faire moins de 50 caractères ";
        }
        return $errors;
    }
}
