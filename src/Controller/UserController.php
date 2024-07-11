<?php

namespace App\Controller;

use App\Model\UserManager;
use PDO;

class UserController extends AbstractController
{
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $lastname = $_POST['lastname'] ?? '';
            $mail = $_POST['mail'] ?? '';
            $password = $_POST['password'] ?? '';
            $profilePicture = $_POST['profile_picture'] ?? null;

            // Vérification et validation des données
            if ($name && $lastname && $mail && $password) {
                $userManager = new UserManager();
                // Hachage du mot de passe
                //memory_cost: la quantité de mémoire à utiliser (en bytes).
                //time_cost:le nombre de passes de l'algorithme.
                //threads:le nombre de threads à utiliser.
                $hashedPassword = password_hash($password, PASSWORD_ARGON2I, ['memory_cost' => 1 << 17,
                                                                              'time_cost' => 4, 
                                                                              'threads' => 2]);
                // Obtention de l'id du rôle "USER"
                $userPrivilegeId = $userManager->getPrivilegeIdByName('USER');
                // Création de l'utilisateur avec le rôle "USER"
                $userManager->createUser($name, $lastname, $mail, $hashedPassword, $profilePicture, $userPrivilegeId);
                // Redirection vers la page de connexion
                header('Location: /login');
                exit();
            } else {
                $error = 'Tous les champs sont obligatoires sauf la photo de profil';
                return $this->twig->render('Auth/register.html.twig', ['error' => $error]);
            }
        }
        return $this->twig->render('Auth/register.html.twig');
    }

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
