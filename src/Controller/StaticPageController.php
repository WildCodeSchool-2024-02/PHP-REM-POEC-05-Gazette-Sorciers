<?php

namespace App\Controller;

use App\Controller\AbstractController;
use PDO;

class StaticPageController extends AbstractController
{
    public function notice()
    {
        return $this->twig->render('Notice/index.html.twig');
    }
}
