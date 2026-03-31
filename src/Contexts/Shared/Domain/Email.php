<?php

declare(strict_types=1);

namespace App\Contexts\Shared\Domain;

use InvalidArgumentException;
use Stringable;

use const FILTER_VALIDATE_EMAIL;

final readonly class Email implements Stringable
{
    private function __construct(private string $value)
    {
    }

    public static function fromString(string $email): self
    {
        $normalized = strtolower(trim($email));
        if ('' === $normalized || !filter_var($normalized, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email address.');
        }

        return new self($normalized);
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
