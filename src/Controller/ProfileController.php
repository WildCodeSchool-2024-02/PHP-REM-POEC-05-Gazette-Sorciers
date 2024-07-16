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
}
