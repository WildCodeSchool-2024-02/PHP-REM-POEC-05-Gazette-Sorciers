<?php

namespace App\Controller;

use App\Model\ContactManager;
use PDO;
use App\Model\RecaptchaManager;

class ContactController extends AbstractController
{
    private RecaptchaManager $recaptchaManager;

    public function __construct()
    {
        parent::__construct();
        $this->recaptchaManager = new RecaptchaManager(RECAPTCHA_SECRET_KEY);
    }
    public function contact()
    {
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupération de la réponse reCAPTCHA
            $recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';
            $remoteIp = $_SERVER['REMOTE_ADDR'];

            // Vérifie la réponse reCAPTCHA
            $recaptchaResult = $this->recaptchaManager->verifyRecaptcha($recaptchaResponse, $remoteIp);

            if (!$recaptchaResult['success']) {
                $error = $recaptchaResult['error'];
                return $this->twig->render('Contact/index.html.twig', ['error' => $error]);
            }

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
