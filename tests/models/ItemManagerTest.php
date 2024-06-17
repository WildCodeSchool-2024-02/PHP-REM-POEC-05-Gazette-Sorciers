<?php

use App\Model\ItemManager;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/config.php';

class ItemManagerTest extends TestCase
{
    public function testSelectAll(): void
    {
        $manager = new ItemManager();
        $this->assertIsArray($manager->selectAll());
    }
}
