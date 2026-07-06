<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use DSFiber\Core\Security\PinHasher;

class PinHasherTest extends TestCase
{
    private PinHasher $pinHasher;

    protected function setUp(): void
    {
        $this->pinHasher = new PinHasher();
    }

    public function test_hash_pin_returns_string()
    {
        $pin = '123456';
        $hash = $this->pinHasher->hash($pin);

        $this->assertIsString($hash);
        $this->assertNotEmpty($hash);
    }

    public function test_verify_pin_with_correct_pin()
    {
        $pin = '123456';
        $hash = $this->pinHasher->hash($pin);

        $this->assertTrue($this->pinHasher->verify($pin, $hash));
    }

    public function test_verify_pin_with_incorrect_pin()
    {
        $pin = '123456';
        $hash = $this->pinHasher->hash($pin);

        $this->assertFalse($this->pinHasher->verify('654321', $hash));
    }

    public function test_hash_pin_is_different_each_time()
    {
        $pin = '123456';
        $hash1 = $this->pinHasher->hash($pin);
        $hash2 = $this->pinHasher->hash($pin);

        $this->assertNotEquals($hash1, $hash2);
    }
}
