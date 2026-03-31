<?php

declare(strict_types=1);

namespace App\Contexts\Identity\Domain\User;

use InvalidArgumentException;
use Stringable;
use Symfony\Component\Uid\Uuid;

final readonly class UserId implements Stringable
{
    private function __construct(private string $value)
    {
    }

    public static function generate(): self
    {
        return new self(Uuid::v4()->toRfc4122());
    }

    public static function fromString(string $value): self
    {
        if ('' === $value) {
            throw new InvalidArgumentException('User id cannot be empty.');
        }

        return new self($value);
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
