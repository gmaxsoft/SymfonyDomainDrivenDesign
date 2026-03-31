<?php

declare(strict_types=1);

namespace App\Contexts\Shared\Domain;

use DateTimeImmutable;

abstract class AbstractDomainEvent implements DomainEventInterface
{
    private readonly DateTimeImmutable $occurredOn;

    public function __construct(?DateTimeImmutable $occurredOn = null)
    {
        $this->occurredOn = $occurredOn ?? new DateTimeImmutable();
    }

    public function occurredOn(): DateTimeImmutable
    {
        return $this->occurredOn;
    }
}
