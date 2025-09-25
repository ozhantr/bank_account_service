<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api/accounts')]
final class AccountController extends AbstractController
{
    #[Route('/me', name: 'api_accounts_me', methods: ['GET'])]
    public function me(#[CurrentUser] ?User $user): JsonResponse
    {
        if (null === $user) {
            return $this->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        $account = $user->getAccount();
        if (null === $account) {
            return $this->json(['error' => 'Account not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'user' => [
                'id' => (string) $user->getId(),
                'email' => $user->getEmail(),
                'createdAt' => $user->getCreatedAt()->format(\DATE_ATOM),
            ],
            'account' => [
                'id' => (string) $account->getId(),
            ],
        ]);
    }
}
