<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ChercheurController extends AbstractController
{
    #[Route('/chercheur', name: 'app_chercheur')]
    public function index(): Response
    {
        return $this->render('chercheur/index.html.twig', [
            'controller_name' => 'ChercheurController',
        ]);
    }
}
