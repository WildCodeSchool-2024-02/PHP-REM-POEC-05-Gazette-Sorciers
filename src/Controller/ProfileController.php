<?php

namespace App\Controller;

use App\Model\UserManager;
use Twig\Environment;

class ProfileController extends AbstractController
{
    protected Environment $twig;
    protected $userModel;


    public function profile(int $id)
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['id'] !== $id) {
            header('Location: /login');
            exit();
        }

        $this->userModel = new UserManager();
        $user = $this->userModel->getUserById($id);
        $comments = $this->userModel->getUserLastComment($id);

        // Vérifier si un message flash est présent
        $message = null;
        if (isset($_SESSION['flash_message'])) {
            $message = $_SESSION['flash_message'];
            // Effacer le message après l'affichage
            unset($_SESSION['flash_message']);
        }

        if (empty($user['profile_picture'])) {
            $defaultImages = [
                '/assets/images/default-1.png',
                '/assets/images/default-2.jpg',
            ];
            $user['profile_picture'] = $defaultImages[array_rand($defaultImages)];
        }
        return $this->twig->render('UserProfile/profile.html.twig', [
            'user' => $user,
            'comments' => $comments,
            'message' => $message,
        ]);
    }
    public function editProfile(int $id)
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['id'] !== $id) {
            header('Location: /login');
            exit();
        }

        $this->userModel = new UserManager();

        $user = $this->userModel->getUserById($id);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $lastname = $_POST['lastname'] ?? '';
            $mail = $_POST['mail'] ?? '';
            $password = $_POST['password'] ?? '';
            $description = $_POST['description'] ?? '';

            if ($name && $lastname && $mail && $description) {
                $this->userModel->updateUser($id, $name, $lastname, $mail, $password, $description);
                // Ajouter un message flash
                $_SESSION['flash_message'] = 'Votre profil a été mis à jour !';

                header('Location: /profile?id=' . $id);
                exit();
            }
        }

        return $this->twig->render('UserProfile/editProfile.html.twig', [
            'user' => $user
        ]);
    }
}
