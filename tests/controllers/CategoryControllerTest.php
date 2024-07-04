<?php

use App\Controller\CategoryController;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/config.php';


class CategoryControllerTest extends TestCase
{
    public function testIndex(): void
    {
        $categorycontroller = new CategoryController();
        $this->assertIsString($categorycontroller->index());
    }
}
