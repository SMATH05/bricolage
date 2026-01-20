<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class ThemeController extends AbstractController
{
    #[Route('/api/theme', name: 'app_theme_update', methods: ['POST'])]
    public function updateTheme(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        $data = json_decode($request->getContent(), true);
        $theme = $data['theme'] ?? 'light';

        if (!in_array($theme, ['light', 'dark'])) {
            return new JsonResponse(['error' => 'Invalid theme'], 400);
        }

        /** @var \App\Entity\User $user */
        $user->setTheme($theme);
        $entityManager->flush();

        return new JsonResponse(['status' => 'success', 'theme' => $theme]);
    }
}
