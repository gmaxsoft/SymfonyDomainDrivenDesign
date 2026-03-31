<?php

declare(strict_types=1);

namespace App\Contexts\Identity\Application\User;

use App\Contexts\Identity\Domain\User\UserRepositoryInterface;
use App\Contexts\Shared\Domain\Email;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final readonly class GetUserByEmailHandler
{
    public function __construct(
        private UserRepositoryInterface $users,
    ) {
    }

    public function __invoke(GetUserByEmailQuery $query): ?UserView
    {
        $email = Email::fromString($query->email);
        $user = $this->users->findByEmail($email);
        if (null === $user) {
            return null;
        }

        return new UserView($user->id()->toString(), $user->email()->toString());
    }
}
