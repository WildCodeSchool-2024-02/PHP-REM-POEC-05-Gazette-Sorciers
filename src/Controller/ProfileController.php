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
        $this->userModel = new UserManager();
        $user = $this->userModel->getUserById($id);
        $comments = $this->userModel->getUserLastComment($id);

        return $this->twig->render('UserProfile/profile.html.twig', [
            'user' => $user,
            'comments' => $comments
        ]);
    }
    public function editProfile(int $id)
    {
        $id = (int)($id);
        var_dump($id);
        // Arrêter l'exécution pour vérifier la valeur de l'id
        // die('ID after conversion');
        // Vérifier si l'id est valide
        if ($id <= 0) {
            // Gérer l'erreur si l'id n'est pas valide
            return $this->twig->render('error.html.twig', [
                'message' => 'ID utilisateur invalide.'
            ]);
        }
        $this->userModel = new UserManager();
        
        $user = $this->userModel->getUserById($id);
        var_dump($user);
        die('User fetched');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $lastname = $_POST['lastname'] ?? '';
            $mail = $_POST['mail'] ?? '';
            $password = $_POST['password'] ?? '';
            $description = $_POST['description'] ?? '';

            if ($name && $lastname && $mail && $description) {
                $this->userModel->updateUser($id, $name, $lastname, $mail, $password, $description);
                header('Location: /profile?id=' . $id);
                exit();
            }
        }

        return $this->twig->render('UserProfile/editProfile.html.twig', [
            'user' => $user
        ]);
        }
}