<?php

namespace App\Controller;

use DateTime;
use App\Model\UserManager;
use App\Model\TopicManager;
use App\Model\CommentManager;
use App\Model\PrivilegeManager;
use App\Controller\AbstractController;
use App\Service\Upload;

class CommentController extends AbstractController
{
    /**
     * List comments
     */
    public function index(int $id): string
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
    public function add(): ?string
    {
        if (!$this->user) {
            header('Location: /login');
            exit();
        }

        $errors = [];
        $fileResponse = "";
        $uploadService = new Upload(); // On suppose que vous avez un service d'upload similaire à celui des sujets

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Nettoyer les données $_POST
            $comment = array_map('trim', $_POST);
            $commentFile = $_FILES['picture'] ?? null;

            // Valider les données du commentaire
            $errors = $this->checkdata($comment, $commentFile);

            // Gérer le téléchargement du fichier si présent
            if ($commentFile && $commentFile['size'] != '0') {
                $fileResponse = $uploadService->uploadFile($commentFile);
            }

            // Si les erreurs sont vides et que le téléchargement a réussi
            if (empty($errors) && gettype($fileResponse) === "string") {
                $comment['created_at'] = (new DateTime())->format('Y-m-d H:i:s');
                $comment['picture'] = $fileResponse;
                $comment['id_user'] = $this->user['id'];

                $commentManager = new CommentManager();
                $commentManager->insert($comment);

                header('Location: /topics/show?id=' . $comment['id_topic']);
                return null;
            }
        }

        return $this->twig->render('comments/add.html.twig', [
            'errors' => $errors,
            'fileResponse' => $fileResponse
        ]);
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
        $extension = pathinfo(
            $commentFile['name'],
            PATHINFO_EXTENSION
        );
        $authorizedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $maxFileSize = 2000000;

        $errors = [];

        if (empty($comment['content'])) {
            $errors[] = 'Le contenu est recquis';
        }
        if (!empty($commentFile)) {
            if ((file_exists($commentFile['name']) && !in_array($extension, $authorizedExtensions))) {
                $errors[] = 'Veuillez sélectionner une image de type Jpg ou Jpeg ou Png ou Gif !';
            }

            if (file_exists($commentFile['name']) && filesize($commentFile['name']) > $maxFileSize) {
                $errors[] = 'Votre fichier doit faire moins de 2Mo !';
            }
        }

        return $errors;
    }
}
