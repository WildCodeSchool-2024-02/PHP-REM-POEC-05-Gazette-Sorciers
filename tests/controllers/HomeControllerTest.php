<?php

use App\Controller\HomeController;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/config.php';


class HomeControllerTest extends TestCase
{
    public function testIndex(): void
    {
        $homeController = new HomeController();
        $this->assertIsString($homeController->index());
    }
}
