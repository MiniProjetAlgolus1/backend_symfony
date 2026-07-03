<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;

final class StatsController extends AbstractController
{
    #[Route('/api/stats', name: 'app_stats', methods:['GET'])]
    public function stats(TaskRepository $taskRepository, UserRepository $userRepository): JsonResponse
    {
        return $this->json([
            'totalUsers' => count($userRepository->findAll()),
            'totalTasks' => count($taskRepository->findAll()),
        ]);
    }
}
