<?php

declare(strict_types=1);

namespace App\Contexts\Identity\Application\User;

final readonly class UserView
{
    public function __construct(
        public string $id,
        public string $email,
    ) {
    }
}
