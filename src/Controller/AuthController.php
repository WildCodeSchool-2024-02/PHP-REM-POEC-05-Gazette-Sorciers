<?php

namespace App\Controller;

use App\Model\UserManager;
use PDO;

class AuthController extends AbstractController
{
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $mail = $_POST['mail'] ?? '';
            $password = $_POST['password'] ?? '';

            $userManager = new UserManager();
            $user = $userManager->getUserByMail($mail);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user'] = $user;
                header('Location: /');
                exit();
            } else {
                $error = 'Email ou mot de passe invalide';
                return $this->twig->render('Auth/login.html.twig', ['error' => $error]);
            }
        }

        return $this->twig->render('Auth/login.html.twig');
    }
}
