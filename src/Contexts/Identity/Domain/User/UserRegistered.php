<?php

declare(strict_types=1);

namespace App\Contexts\Identity\Domain\User;

use App\Contexts\Shared\Domain\AbstractDomainEvent;
use DateTimeImmutable;

final class UserRegistered extends AbstractDomainEvent
{
    public function __construct(
        private readonly string $userId,
        private readonly string $email,
        ?DateTimeImmutable $occurredOn = null,
    ) {
        parent::__construct($occurredOn);
    }

    public function userId(): string
    {
        return $this->userId;
    }

    public function email(): string
    {
        return $this->email;
    }
}
