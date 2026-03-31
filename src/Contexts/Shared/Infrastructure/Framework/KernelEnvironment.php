<?php

declare(strict_types=1);

namespace App\Contexts\Shared\Infrastructure\Framework;

/**
 * Small framework adapter so Shared Infrastructure is not empty;
 * prefer injecting parameters or env vars in real apps.
 */
final readonly class KernelEnvironment
{
    public function __construct(
        private string $environment,
        private bool $debug,
    ) {
    }

    public function environment(): string
    {
        return $this->environment;
    }

    public function debug(): bool
    {
        return $this->debug;
    }
}
