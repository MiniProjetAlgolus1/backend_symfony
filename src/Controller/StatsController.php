<?php

namespace App\Controller;

use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class StatsController extends AbstractController
{
    #[Route('/api/stats', name: 'api_stats', methods: ['GET'])]
    public function stats(TaskRepository $taskRepository, UserRepository $userRepository): JsonResponse
    {
        return $this->json([
            'totalUsers' => count($userRepository->findAll()),
            'totalTasks' => count($taskRepository->findAll()),
            'tasksByStatus' => $taskRepository->countByStatus(),
        ]);
    }
}
