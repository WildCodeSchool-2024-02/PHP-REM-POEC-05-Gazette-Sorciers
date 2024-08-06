<?php

namespace App\Controller;

use App\Model\TopicManager;
use App\Controller\AbstractController;
use App\Model\CategoryManager;
use App\Model\CommentManager;
use App\Model\PrivilegeManager;
use App\Model\UserManager;
use App\Service\Upload;
use DateTime;

use function PHPUnit\Framework\fileExists;

class TopicController extends AbstractController
{
    /**
     * List items
     */
    public function index(string $id): string
    {
        $topicManager = new TopicManager();
        $userManager = new UserManager();
        $categoryId = (int) $id;
        $topics = $topicManager->selectAllByCategory($categoryId);
        $topicUsers = [];
        foreach ($topics as $topic) {
            $topicUsers[$topic['id']] = $userManager->selectOneById($topic['id_user']);
        }

        return $this->twig->render('Topic/index.html.twig', [
            'topics' => $topics,
            'topicUsers' => $topicUsers,
            'categoryId' => $categoryId,
        ]);
    }

    /**
     * Show informations for a specific item
     */

    public function show(int $id): string
    {
        $topicManager = new TopicManager();
        $commentManager = new CommentManager();
        $userManager = new UserManager();

        // Récupérer le topic
        $topic = $topicManager->selectOneById($id);
        // Récupérer l'utilisateur qui a créé le topic
        $topicUser = $userManager->selectOneById($topic['id_user']);

        // Récupérer les commentaires
        $comments = $commentManager->selectAllByTopic($id);
        $commentUsers = [];

        // Récupérer les utilisateurs pour chaque commentaire
        foreach ($comments as $comment) {
            $commentUsers[$comment['id']] = $userManager->selectOneById($comment['id_user']);
        }

        //verifie si le fichier existe
        if (file_exists(dirname(__DIR__, 2) . '/public/upload/' . $topic['picture'])) {
            $topic["fileExist"] = true;
        }

        return $this->twig->render('Topic/show.html.twig', [
            'topic' => $topic,
            'topicUser' => $topicUser,
            'comments' => $comments,
            'commentUsers' => $commentUsers,
        ]);
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

        $categoryManager = new CategoryManager();
        $category = $categoryManager->selectOneById($id);

        $errors = [];
        $fileResponse = "";
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // clean $_POST data
            $topic = array_map('trim', $_POST);
            $topicFile = array_map('trim', $_FILES['picture']);
            $errors = $this->validate($topic);

            if (($topicFile['size'] != '0')) {
                $uploadService = new Upload();
                $fileResponse = $uploadService->uploadFile($topicFile);
            }
            if (empty($errors) && gettype($fileResponse) === "string") {
                $topic['created_at'] = (new DateTime())->format('Y-m-d H:i:s');
                $topic['picture'] = $fileResponse;
                $topic['id_category'] = $category['id'];
                $topic['id_user'] = $this->user['id'];
                $topicManager = new TopicManager();
                $id = $topicManager->insert($topic);

                header('Location: /topics/show?id=' . $id);
                return null;
            }
        }
        return $this->twig->render('topic/add.html.twig', [
            'errors' => $errors,
            'category' => $category,
            'fileResponse' => $fileResponse
        ]);
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

    private function validate(array $topic): array
    {
        $errors = [];

        if ($topic['title'] === '' || empty($topic['title'])) {
            $errors[] = 'Votre titre est vide !';
        }

        if ($topic['content'] === '' || empty($topic['content'])) {
            $errors[] = 'Votre description est vide !';
        }

        return $errors;
    }
}
