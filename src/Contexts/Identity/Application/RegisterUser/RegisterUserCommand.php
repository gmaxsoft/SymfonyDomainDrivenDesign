<?php

declare(strict_types=1);

namespace App\Contexts\Identity\Application\RegisterUser;

use App\Contexts\Shared\Application\Command\CommandInterface;

final readonly class RegisterUserCommand implements CommandInterface
{
    public function __construct(
        public string $email,
        public string $passwordPlain,
    ) {
    }
}
