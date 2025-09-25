<?php

namespace App\Controller;

use DateTimeImmutable;
use DateTimeZone;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class HealthController extends AbstractController
{
    #[Route('/api/health', name: 'api_health', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        return $this->json(['status' => 'ok', 'time' => (new DateTimeImmutable('now', new DateTimeZone('UTC'))->format(\DATE_ATOM))]);
    }
}
