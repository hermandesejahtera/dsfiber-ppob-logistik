<?php

namespace DSFiber\Core\Security;

/**
 * PIN Hashing & Validation
 */
class PinHasher
{
    private string $salt;

    public function __construct(string $salt)
    {
        $this->salt = $salt;
    }

    /**
     * Hash PIN dengan salt
     */
    public function hash(string $pin): string
    {
        return hash('sha256', $pin . $this->salt);
    }

    /**
     * Verify PIN
     */
    public function verify(string $pin, string $hash): bool
    {
        return hash_equals($this->hash($pin), $hash);
    }
}
