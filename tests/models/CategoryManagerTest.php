<?php

use App\Model\CategoryManager;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/config.php';

class CategoryManagerTest extends TestCase
{
    public function testSelectAll(): void
    {
        $manager = new CategoryManager();
        $this->assertIsArray($manager->selectAll());
    }
}
