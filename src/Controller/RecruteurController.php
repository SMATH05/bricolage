<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Recruteur;
use App\Form\RecruteurType;

final class RecruteurController extends AbstractController
{
    #[Route('/recruteur', name: 'app_recruteur')]
    public function index(): Response
    {
        return $this->render('recruteur/index.html.twig', [
            'controller_name' => 'RecruteurController',
        ]);
    }

    #[Route('/recruteur/ajouter', name: 'app_recruteur_ajouter')]
    public function ajouterRecruteur(Request $request, EntityManagerInterface $entityManager): Response
    {
        $recruteur = new Recruteur();

        $form = $this->createForm(RecruteurType::class, $recruteur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($recruteur);
            $entityManager->flush();

            return $this->redirectToRoute('app_recruteur');
        }

        return $this->render('recruteur/ajout.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
}
