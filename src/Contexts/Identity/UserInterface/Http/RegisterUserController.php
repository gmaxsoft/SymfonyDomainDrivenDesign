<?php

declare(strict_types=1);

namespace App\Contexts\Identity\UserInterface\Http;

use App\Contexts\Identity\Application\IdentityApplicationService;
use DomainException;
use InvalidArgumentException;
use JsonException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use const JSON_THROW_ON_ERROR;

final class RegisterUserController
{
    public function __construct(
        private readonly IdentityApplicationService $identityApplication,
    ) {
    }

    #[Route(path: '/identity/register', name: 'identity_register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        try {
            $payload = json_decode($request->getContent() ?: '{}', true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return new JsonResponse(['error' => 'Invalid JSON.'], Response::HTTP_BAD_REQUEST);
        }
        $email = (string) ($payload['email'] ?? '');
        $password = (string) ($payload['password'] ?? '');

        try {
            $this->identityApplication->registerUser($email, $password);
        } catch (InvalidArgumentException) {
            return new JsonResponse(['error' => 'Invalid input.'], Response::HTTP_BAD_REQUEST);
        } catch (DomainException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_CONFLICT);
        }

        return new JsonResponse(['status' => 'created'], Response::HTTP_CREATED);
    }

    #[Route(path: '/identity/users/by-email', name: 'identity_user_by_email', methods: ['GET'])]
    public function byEmail(Request $request): JsonResponse
    {
        $email = (string) $request->query->get('email', '');
        try {
            $user = $this->identityApplication->getUserByEmail($email);
        } catch (InvalidArgumentException) {
            return new JsonResponse(['error' => 'Invalid email.'], Response::HTTP_BAD_REQUEST);
        }

        if (null === $user) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse(['id' => $user->id, 'email' => $user->email]);
    }
}
