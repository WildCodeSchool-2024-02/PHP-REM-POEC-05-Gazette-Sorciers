<?php

namespace App\Controller;

use DateTime;
use App\Model\UserManager;
use App\Model\TopicManager;
use App\Model\CommentManager;
use App\Model\PrivilegeManager;
use App\Controller\AbstractController;

class CommentController extends AbstractController
{
    /**
     * List comments
     */
    public function index(string $id): string
    {
        $commentManager = new CommentManager();
        $comments = $commentManager->selectAllByTopic($id);
        return $this->twig->render('comments/index.html.twig', ['comments' => $comments]);
    }

    /**
     * Show informations for a specific comment
     */
    public function show(int $id): string
    {
        $commentManager = new CommentManager();
        $comment = $commentManager->selectOneById($id);

        return $this->twig->render('comments/show.html.twig', ['comment' => $comment]);
    }

    /**
     * Add a new comment
     */
    public function add(int $id): ?string
    {
        if (!$this->user) {
            header('Location: /login');
            exit();
        }

        $privilegeManger = new PrivilegeManager();
        $usermanager = new UserManager();

        $user = $usermanager->selectOneById($this->user['id']);
        if (!$privilegeManger->isUserAdmin($user['id_privilege'])) {
            header('Location: /');
            exit();
        }

        $topicManager = new TopicManager();
        $topic = $topicManager->selectOneById($id);

        $errors = [];
        $uploadDir = 'upload/';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // clean $_POST data
            $comment = array_map('trim', $_POST);
            $commentFile = array_map('trim', $_FILES['picture']);
            $errors = $this->checkdata($comment, $commentFile);
            // TODO validations (length, format...)

            if (empty($errors)) {
                if (($commentFile['size'] != '0')) {
                    $fileName = (new DateTime())->format('Y-m-d-H-i-s') . '-' . $commentFile['name'];
                    $topic['picture'] = $uploadDir . basename($fileName);
                    $uploadFile = $uploadDir . basename($fileName);
                    move_uploaded_file($_FILES['picture']['tmp_name'], $uploadFile);
                }
                $comment['created_at'] = (new DateTime())->format('Y-m-d H:i:s');
                $comment['id_topic'] = $topic['id'];
                $comment['id_user'] = $this->user['id'];
                $commentManager = new CommentManager();
                $id = $commentManager->insert($comment);

                header('Location: /comments/show?id=' . $id);
                return null;
            }
        }

        return $this->twig->render('comments/add.html.twig', ['errors' => $errors, 'topic' => $topic]);
    }

    /**
     * Delete a specific comment
     */
    public function delete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = trim($_POST['id']);
            $commentManager = new CommentManager();
            $commentManager->delete((int)$id);

            header('Location: /comments');
        }
    }

    //DRY way to validate data in controller
    public function checkdata(array $comment, array $commentFile): array
    {

        $extension = pathinfo($comment['picture'], PATHINFO_EXTENSION);
        $authorizedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $maxFileSize = 2000000;

        $errors = [];

        if (empty($comment['content'])) {
            $errors[] = 'Le contenu est recquis';
        }

        if ((file_exists($commentFile['picture']) && !in_array($extension, $authorizedExtensions))) {
            $errors[] = 'Veuillez sélectionner une image de type Jpg ou Jpeg ou Png ou Gif !';
        }

        if (file_exists($commentFile['picture']) && filesize($commentFile['picture']) > $maxFileSize) {
            $errors[] = 'Votre fichier doit faire moins de 2Mo !';
        }

        return $errors;
    }
}
