<?php

namespace App\Controller;

use App\Model\PrivilegeManager;
use App\Model\RecaptchaManager;
use App\Model\UserManager;
use App\Model\TokenManager;
use App\Model\TopicManager;
use App\Model\CommentManager;
use DateTime;
use PDO;
use mail\mail\mail;
use PHPMailer\PHPMailer\PHPMailer;
use App\Service\Upload;

class UserController extends AbstractController
{
    private RecaptchaManager $recaptchaManager;

    public function __construct()
    {
        parent::__construct();
        $this->recaptchaManager = new RecaptchaManager(RECAPTCHA_SECRET_KEY);
    }
    // Fonction pour définir un cookie
    public function setCookie($name, $value, $expire)
    {
        setcookie($name, $value, $expire, "/");
        // "/" signifie que le cookie est disponible dans tout le domaine
    }

    // Fonction pour récupérer un cookie
    public function getCookie($name)
    {
        return isset($_COOKIE[$name]) ? $_COOKIE[$name] : null;
    }

    // Fonction pour supprimer un cookie
    public function deleteCookie($name)
    {
        setcookie($name, "", time() - 3600, "/");
    }

    public function register()
    {
        $defaultImages = [
            '/assets/images/default-1.png',
            '/assets/images/default-2.jpg',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupération de la réponse reCAPTCHA
            $recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';
            $remoteIp = $_SERVER['REMOTE_ADDR'];

            // Vérifie la réponse reCAPTCHA
            $recaptchaResult = $this->recaptchaManager->verifyRecaptcha($recaptchaResponse, $remoteIp);

            if (!$recaptchaResult['success']) {
                $error = $recaptchaResult['error'];
                return $this->twig->render('Auth/register.html.twig', ['error' => $error]);
            }

            $name = $_POST['name'] ?? '';
            $lastname = $_POST['lastname'] ?? '';
            $mail = $_POST['mail'] ?? '';
            $password = $_POST['password'] ?? '';
            $profilePicture = $_POST['profile_picture'] ?? $defaultImages[array_rand($defaultImages)];

            if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
                $uploadService = new Upload();
                $fileResponse = $uploadService->uploadFile($_FILES['profile_picture']);

                if (is_string($fileResponse)) {
                    $profilePicture = $fileResponse;
                } else {
                    $error = 'Erreur lors du téléchargement de la photo de profil';
                    return $this->twig->render('Auth/register.html.twig', ['error' => $error]);
                }
            }
            // Vérification et validation des données
            if ($name && $lastname && $mail && $password) {
                $userManager = new UserManager();
                // Hachage du mot de passe
                //memory_cost: la quantité de mémoire à utiliser (en bytes).
                //time_cost:le nombre de passes de l'algorithme.
                //threads:le nombre de threads à utiliser.
                $hashedPassword = password_hash($password, PASSWORD_ARGON2I, [
                    'memory_cost' => 1 << 17,
                    'time_cost' => 4,
                    'threads' => 2
                ]);
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
            //we should'nt put the password, nor privillege into user profile session
            if ($user && password_verify($password, $user['password'])) {
                $privilegeManager = new PrivilegeManager();
                $isUserAdmin = $privilegeManager->isUserAdmin($user['id_privilege']);
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'lastname' => $user['lastname'],
                    'created_at' => $user['created_at'],
                    'mail' => $user['mail'],
                    'profile_picture' => $user['profile_picture'],
                    'id_privilege' => $user['id_privilege'],
                    'isUserAdmin' => $isUserAdmin
                ];
                header('Location: /');
                exit();
            } else {
                $error = 'Email ou mot de passe invalide';
                return $this->twig->render('Auth/login.html.twig', ['error' => $error]);
            }
        }

        return $this->twig->render('Auth/login.html.twig');
    }
    public function logout()
    {
        session_destroy();
        unset($_SESSION['user']);
        header('Location: /');
        exit();
    }


    public function listUsers()
    {
        $this->checkAdminPrivilege();
        $userManager = new UserManager();
        $users = $userManager->getAllUsers();

        $cookiesAccepted = $this->getCookie('cookies_accepted');

        return $this->twig->render('Users/index.html.twig', [
            'users' => $users,
            'cookiesAccepted' => $cookiesAccepted
        ]);
    }
    public function delete()
    {
        $this->checkAdminPrivilege();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $userManager = new UserManager();
            $commentManager = new CommentManager();
            $topicManager = new TopicManager();
            $commentManager->giveToAnonymous($id);
            $topicManager->giveToAnonymous($id);
            $userManager->deleteUserById($id);
            header('Location: /users');
            exit();
        }
        header('Location: /users');

        exit();
    }

    public function forgotPassword()
    {
        $result = false;
        $error = false;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $mail = $_POST['mail'] ?? '';
            $userManager = new UserManager();
            $user = $userManager->getUserByMail($mail);
            if ($user) {
                $tokenManager = new TokenManager();
                $token = $tokenManager->getToken($user['id']);
                $currTime = new DateTime();

                if (!$token) {
                    $key = uniqid();
                    $tokenManager->create($user['id'], $key);
                    $token = $tokenManager->getToken($user['id']);
                }
                $interval = (new DateTime($token['created_at']))->diff($currTime);
                if ($interval->d > 0 || $interval->h > 0 || $interval->i > 10) {
                    $tokenManager->delete($user['id']);
                    $key = uniqid();
                    $tokenManager->create($user['id'], $key);
                    $token = $tokenManager->getToken($user['id']);
                }

                $smtp = $this->setupMailTo($user['id'], $mail, $token['key']);
                if ($smtp->send()) {
                    $result = "Un message vous à été envoyé.";
                } else {
                    $error = "an error occured" . $smtp->ErrorInfo;
                }
            } else {
                $error = "No user found";
            }
        }
        return $this->twig->render('Auth/forgotPassword.html.twig', ['result' => $result, 'error' => $error]);
    }
    public function resetPassword($tokenUrl)
    {
        if (!isset($_GET['token'])) {
            header('Location: /');
            exit();
        }
        $token = $tokenUrl;
        $idUser = $_GET['id'];
        $tokenManager = new TokenManager();
        $getToken = $tokenManager->getToken($idUser);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $password = $_POST['password'] ?? '';
            $hashedPassword = password_hash($password, PASSWORD_ARGON2I, [
                'memory_cost' => 1 << 17,
                'time_cost' => 4,
                'threads' => 2
            ]);
            $userManager = new UserManager();
            $userManager->updatePassword($idUser, $hashedPassword);
            $tokenManager = new TokenManager();
            $tokenManager->delete($idUser);
            header('Location: /login');
            exit();
        }


        $userManager = new UserManager();
        $user = $userManager->getUserById($idUser);
        if (!$user) {
            header('Location: /');
            exit();
        }
        $tokenManager = new TokenManager();
        $getToken = $tokenManager->getToken($idUser);
        if (!$getToken) {
            header('Location: /');
            exit();
        }
        $currTime = new DateTime();
        $interval = (new DateTime($getToken['created_at']))->diff($currTime);
        //token expiration time
        if ($interval->d > 0 || $interval->h > 0 || $interval->i > 10) {
            $tokenManager->delete($idUser);
            header('Location: /');
            exit();
        }

        if ($getToken['key'] != $token) {
            header('Location: /');
            exit();
        }
        return $this->twig->render('Auth/resetPassword.html.twig', ['idUser' => $idUser, 'token' => $token]);
    }

    public function setupMailTo($id, $email, $token): PHPMailer
    {
        $userManager = new UserManager();
        $user = $userManager->getUserById($id);
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Port = 587;
        $mail->Username = SMTP_USER;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->SMTPSecure = 'tls';
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->setFrom(SEND_FROM, SEND_FROM_NAME);
        $mail->addReplyTo(SMTP_USER);
        $mail->Subject = 'Demande de changement de votre mot de passe';
        $mail->msgHTML($this->twig->render('UserProfile/Reset.html.twig', [
            'name' => $user['name'],
            'idUser' => $id,
            'token' => $token
        ]));
        $mail->Body = $this->twig->render('UserProfile/Reset.html.twig', [
            'name' => $user['name'],
            'idUser' => $id,
            'token' => $token
        ]);
        return $mail;
    }
}
