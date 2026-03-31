<?php

declare(strict_types=1);

namespace App\Contexts\Identity\Application;

use App\Contexts\Identity\Application\RegisterUser\RegisterUserCommand;
use App\Contexts\Identity\Application\User\GetUserByEmailQuery;
use App\Contexts\Identity\Application\User\UserView;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

/**
 * Application-facing API for the Identity bounded context (command/query dispatch).
 */
final readonly class IdentityApplicationService
{
    public function __construct(
        #[Autowire(service: 'command.bus')]
        private MessageBusInterface $commandBus,
        #[Autowire(service: 'query.bus')]
        private MessageBusInterface $queryBus,
    ) {
    }

    public function registerUser(string $email, string $plainPassword): void
    {
        $this->commandBus->dispatch(new RegisterUserCommand($email, $plainPassword));
    }

    public function getUserByEmail(string $email): ?UserView
    {
        $envelope = $this->queryBus->dispatch(new GetUserByEmailQuery($email));
        $handled = $envelope->last(HandledStamp::class);

        return $handled?->getResult();
    }
}
