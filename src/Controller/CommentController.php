<?php

namespace App\Controller;

use App\Model\CommentManager;
use App\Controller\AbstractController;
use DateTime;

class CommentController extends AbstractController
{
    /**
     * List comments
     */
    public function index(): string
    {
        $commentManager = new CommentManager();
        $comments = $commentManager->selectAll('created_at');

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
     * Edit a specific comment
     */
    public function edit(int $id): ?string
    {
        $commentManager = new CommentManager();
        $comment = $commentManager->selectOneById($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // clean $_POST data
            $comment = array_map('trim', $_POST);

            // TODO validations (length, format...)
            // if validation is ok, update and redirection
            if ($this->validate(true, $comment)) {
                $comment['is_modified'] = 1;
                $commentManager->update($comment);

                header('Location: /comments/show?id=' . $id);
                // we are redirecting so we don't want any content rendered
                return null;
            }
        }

        return $this->twig->render('comments/edit.html.twig', [
            'comment' => $comment,
        ]);
    }

    /**
     * Add a new comment
     */
    public function add(): ?string
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // clean $_POST data
            $comment = array_map('trim', $_POST);
            // TODO validations (length, format...)

            // if validation is ok, insert and redirection
            if ($this->validate(false, $comment)) {
                $comment['created_at'] = (new DateTime())->format('Y-m-d H:i:s');
                $commentManager = new CommentManager();
                $id = $commentManager->insert($comment);

                header('Location: /comments/show?id=' . $id);
                return null;
            }
        }

        return $this->twig->render('comments/add.html.twig');
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
    private function validate(bool $edit, array $comment): bool
    {
        // Content validation
        if (empty($comment['content'])) {
            return false;
        }

        // User ID and Topic ID validation
        if (!isset($comment['id_user']) || !filter_var($comment['id_user'], FILTER_VALIDATE_INT)) {
            return false;
        }

        if (!isset($comment['id_topic']) || !filter_var($comment['id_topic'], FILTER_VALIDATE_INT)) {
            return false;
        }

        // ID validation only in edit mode
        if ($edit && (!isset($comment['id']) || !filter_var($comment['id'], FILTER_VALIDATE_INT))) {
            return false;
        }

        return true;
    }
}
