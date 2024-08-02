<?php

namespace App\Controller;

use App\Model\UserManager;
use Twig\Environment;

class ProfileController extends AbstractController
{
    protected Environment $twig;
    public $userModel;


    public function profile()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit();
        }
        $this->userModel = new UserManager();
        $comments = $this->userModel->getUserLastComment($_SESSION['user']['id']);
        // Vérifier si un message flash est présent
        $message = null;
        if (isset($_SESSION['flash_message'])) {
            $message = $_SESSION['flash_message'];
            // Effacer le message après l'affichage
            unset($_SESSION['flash_message']);
        }

        return $this->twig->render('UserProfile/profile.html.twig', [
            'comments' => $comments,
            'message' => $message,
        ]);
    }
    public function editProfile()
    {
        $this->userModel = new UserManager();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $lastname = $_POST['lastname'] ?? '';
            $mail = $_POST['mail'] ?? '';
            $password = $_POST['password'] ?? '';
            $description = $_POST['description'] ?? '';

            if ($name && $lastname && $mail && $description) {
                $this->userModel->updateUser($_SESSION['user']['id'], $name, $lastname, $mail, $password, $description);
                // Ajouter un message flash
                $_SESSION['flash_message'] = 'Votre profil a été mis à jour !';

                header('Location: /profile');
                exit();
            }
        }

        return $this->twig->render('UserProfile/editProfile.html.twig');
    }
}
