<?php

namespace App\Controller;

use App\Model\UserManager;
use Twig\Environment;
use App\Service\Upload;


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
        $userModel = new UserManager();
        $uploadService = new Upload();
        $userId = $_SESSION['user']['id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $lastname = $_POST['lastname'] ?? '';
            $mail = $_POST['mail'] ?? '';
            $password = $_POST['password'] ?? '';
            $description = $_POST['description'] ?? '';

            $currentProfilePicture = $_POST['current_profile_picture'] ?? '';

            if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
                $fileResponse = $uploadService->uploadFile($_FILES['profile_picture']);
                if (is_string($fileResponse)) {
                    $profilePicture = $fileResponse;
                } else {
                    $error = 'Erreur lors du téléchargement de la photo de profil';
                    return $this->twig->render('UserProfile/editProfile.html.twig', ['error' => $error, 'user' => $userModel->selectOneById($userId)]);
                }
            } else {
                $profilePicture = $currentProfilePicture;
            }

            // Mise à jour des informations utilisateur
            if ($name && $lastname && $mail && $description) {
                $userModel->updateUser($userId, $name, $lastname, $mail, $password, $description, $profilePicture);
                $_SESSION['flash_message'] = 'Votre profil a été mis à jour !';
                header('Location: /profile');
                exit();
            }
        }

        $user = $userModel->selectOneById($userId);
        return $this->twig->render('UserProfile/editProfile.html.twig', ['user' => $user]);
    }
}
