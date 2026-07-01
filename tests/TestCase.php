<?php

namespace Tests;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;

abstract class TestCase extends PHPUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Load environment
        require_once __DIR__ . '/../config/bootstrap.php';
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
