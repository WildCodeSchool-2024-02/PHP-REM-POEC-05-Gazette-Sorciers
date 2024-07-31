<?php

namespace App\Controller;

use App\Model\PrivilegeManager;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;
use App\Model\UserManager;
use SebastianBergmann\Type\FalseType;

abstract class AbstractController
{
    /**
     * Instance du moteur de templates Twig.
     *
     * @var Environment
     */
    protected Environment $twig;

    /**
     * Informations sur l'utilisateur connecté.
     * Peut être un tableau associatif contenant les détails de
     * l'utilisateur ou `false` si l'utilisateur n'est pas connecté.
     *
     * @var array|false
     */
    protected array|false $user;

    /**
     * Constructeur de la classe.
     * Initialise le moteur de templates Twig et les informations sur l'utilisateur.
     * Démarre la session si elle n'est pas déjà démarrée.
     */
    public function __construct()
    {
        //je démarre la session si elle n'esdt pas déja démarrée
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $loader = new FilesystemLoader(APP_VIEW_PATH);
        $this->twig = new Environment(
            $loader,
            [
                'cache' => false,
                'debug' => true,
            ]
        );
        $this->twig->addExtension(new DebugExtension());
        $userManager = new UserManager();
        $this->user = $this->initializeUser($userManager);
        $this->twig->addGlobal('user', $this->user);
    }

    /**
     * Vérifie si l'utilisateur connecté a les privilèges d'administrateur.
     * Redirige vers la page de connexion si l'utilisateur n'est pas connecté ou n'est pas un administrateur.
     */
    protected function checkAdminPrivilege()
    {
        // je vérifie si le user est connecté
        if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
            header('Location: /login');
            exit();
        }


        $user = $_SESSION['user'];

        // j'initialise PrivilegeManager et vérifie si l'utilisateur est admin
        $privilegeManager = new PrivilegeManager();
        if (!$privilegeManager->isUserAdmin($user['id_privilege'])) {
            header('Location: /');
            exit();
        }
    }




    /**
     * Vérifie si l'utilisateur a les privilèges nécessaires pour accéder à la page.
     *
     * Cette fonction vérifie si une session utilisateur est active.
     * Si l'utilisateur n'est pas connecté (c'est-à-dire si la variable de session
     * `$_SESSION['user']` n'est pas définie),
     * il est redirigé vers la page de connexion.
     *
     * @return void
     */
    protected function checkUserPrivilege()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit();
        }
    }

    /**
     * Initialise les informations de l'utilisateur à partir de la session.
     * Vérifie également les privilèges d'administration.
     *
     * @param UserManager $userManager Instance de UserManager pour récupérer les informations utilisateur.
     * @return array|false Tableau contenant les détails de l'utilisateur ou `false` si l'utilisateur n'est pas trouvé.
     */
    protected function initializeUser(UserManager $userManager): array|false
    {
        if (isset($_SESSION['user']['id'])) {
            $user = $userManager->getUserById($_SESSION['user']['id']);
            if ($user) {
                // jinitialise PrivilegeManager et vérifie les privilèges d'administration
                $privilegeManager = new PrivilegeManager();
                $isUserAdmin = $privilegeManager->isUserAdmin($user['id_privilege']);
                $user['isUserAdmin'] = $isUserAdmin;
                return $user;
            }
        }
        return false;
    }
}
