<?php

declare(strict_types=1);

namespace App\Contexts\Identity\Infrastructure\Persistence;

use App\Contexts\Identity\Domain\User\User;
use App\Contexts\Identity\Domain\User\UserId;
use App\Contexts\Identity\Domain\User\UserRepositoryInterface;
use App\Contexts\Shared\Domain\Email;
use Doctrine\ORM\EntityManagerInterface;

final readonly class DoctrineUserRepository implements UserRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function save(User $user): void
    {
        $this->entityManager->persist($user);
    }

    public function findByEmail(Email $email): ?User
    {
        return $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email->toString()]);
    }

    public function findById(UserId $id): ?User
    {
        return $this->entityManager->find(User::class, $id->toString());
    }
}
