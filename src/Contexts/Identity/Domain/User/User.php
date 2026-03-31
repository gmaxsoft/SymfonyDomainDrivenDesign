<?php

declare(strict_types=1);

namespace App\Contexts\Identity\Domain\User;

use App\Contexts\Shared\Domain\Email;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'identity_users')]
class User
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private string $email;

    #[ORM\Column(type: 'string')]
    private string $passwordHash;

    /** @var array<int, object> */
    private array $domainEvents = [];

    private function __construct()
    {
    }

    public static function register(Email $email, string $passwordHash): self
    {
        $self = new self();
        $userId = UserId::generate();
        $self->id = $userId->toString();
        $self->email = $email->toString();
        $self->passwordHash = $passwordHash;
        $self->recordThat(new UserRegistered($self->id, $self->email));

        return $self;
    }

    public function id(): UserId
    {
        return UserId::fromString($this->id);
    }

    public function email(): Email
    {
        return Email::fromString($this->email);
    }

    /**
     * @return list<object>
     */
    public function pullDomainEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];

        return $events;
    }

    private function recordThat(object $event): void
    {
        $this->domainEvents[] = $event;
    }
}
