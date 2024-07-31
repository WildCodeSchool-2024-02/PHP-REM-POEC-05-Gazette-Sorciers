<?php

namespace App\Controller;

class EasterEggController extends AbstractController
{
    public function show()
    {
        return $this->twig->render('Bonus/easteregg.html.twig');
    }
}
