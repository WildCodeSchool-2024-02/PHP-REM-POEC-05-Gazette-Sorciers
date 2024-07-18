<?php

namespace App\Controller;

use App\Model\ContactManager;
use PDO;

class ContactController extends AbstractController
{
    public function contact()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $message = $_POST['message'] ?? '';

            // Vérification et validation des données
            if ($name && $email && $message) {
                $contactManager = new ContactManager();
                $contactManager->saveContact($name, $email, $message);

                // Rediriger ou afficher un message de succès si nécessaire
                header('Location: /index');
                exit();
            } else {
                $error = 'Tous les champs sont obligatoires';
                return $this->twig->render('Contact/index.html.twig', ['error' => $error]);
            }
        }

        return $this->twig->render('Contact/index.html.twig');
    }
}
