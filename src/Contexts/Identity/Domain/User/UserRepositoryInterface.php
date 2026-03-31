<?php

declare(strict_types=1);

namespace App\Contexts\Identity\Domain\User;

use App\Contexts\Shared\Domain\Email;

interface UserRepositoryInterface
{
    public function save(User $user): void;

    public function findByEmail(Email $email): ?User;

    public function findById(UserId $id): ?User;
}
