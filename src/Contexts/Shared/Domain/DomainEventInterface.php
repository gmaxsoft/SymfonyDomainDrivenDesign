<?php

declare(strict_types=1);

namespace App\Contexts\Shared\Domain;

use DateTimeImmutable;

interface DomainEventInterface
{
    public function occurredOn(): DateTimeImmutable;
}
