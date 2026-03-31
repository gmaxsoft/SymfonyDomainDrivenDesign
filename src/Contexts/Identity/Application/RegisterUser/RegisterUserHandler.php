<?php

declare(strict_types=1);

namespace App\Contexts\Identity\Application\RegisterUser;

use App\Contexts\Identity\Domain\User\User;
use App\Contexts\Identity\Domain\User\UserRepositoryInterface;
use App\Contexts\Shared\Domain\Email;
use DomainException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\PasswordHasher\Hasher\NativePasswordHasher;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class RegisterUserHandler
{
    public function __construct(
        private UserRepositoryInterface $users,
        private NativePasswordHasher $passwordHasher,
    ) {
    }

    public function __invoke(RegisterUserCommand $command): void
    {
        $email = Email::fromString($command->email);
        if (null !== $this->users->findByEmail($email)) {
            throw new DomainException('A user with this email is already registered.');
        }

        $hash = $this->passwordHasher->hash($command->passwordPlain);
        $user = User::register($email, $hash);
        $this->users->save($user);
    }
}
