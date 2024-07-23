<?php

namespace App\Controller;

use App\Model\ContactManager;
use PDO;

class ContactController extends AbstractController
{
    public function contact()
    {
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $message = $_POST['message'] ?? '';

            if ($name && $email && $message) {
                $contactManager = new ContactManager();
                $contactManager->saveContact($name, $email, $message);

                return $this->twig->render('Contact/index.html.twig', ['success' => true]);
            } else {
                $error = 'Tous les champs sont obligatoires';
            }
        }

        return $this->twig->render('Contact/index.html.twig', ['error' => $error]);
    }
}
