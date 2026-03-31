<?php

declare(strict_types=1);

namespace App\Contexts\Identity\Application\User;

use App\Contexts\Shared\Application\Query\QueryInterface;

final readonly class GetUserByEmailQuery implements QueryInterface
{
    public function __construct(
        public string $email,
    ) {
    }
}
