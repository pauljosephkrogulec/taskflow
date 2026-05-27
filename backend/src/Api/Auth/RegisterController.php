<?php

declare(strict_types=1);

namespace App\Api\Auth;

use App\Application\Auth\RegisterCommand;
use App\Application\Auth\RegisterHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class RegisterController extends AbstractController
{
    public function __construct(
        private readonly RegisterHandler $handler,
    ) {}

    #[Route('/auth/register', name: 'auth_register', methods: ['POST'])]
    public function __invoke(
        #[MapRequestPayload] RegisterRequest $dto,
    ): JsonResponse {
        try {
            $user = $this->handler->handle(
                new RegisterCommand($dto->email, $dto->password, $dto->name)
            );
        } catch (\DomainException $e) {
            return $this->json(['message' => $e->getMessage()], Response::HTTP_CONFLICT);
        }

        return $this->json([
            'id'    => $user->id(),
            'email' => $user->email()->value(),
            'name'  => $user->name(),
        ], Response::HTTP_CREATED);
    }
}
