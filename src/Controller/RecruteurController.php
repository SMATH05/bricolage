<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RecruteurController extends AbstractController
{
    #[Route('/recruteur', name: 'app_recruteur')]
    public function index(): Response
    {
        return $this->render('recruteur/index.html.twig', [
            'controller_name' => 'RecruteurController',
        ]);
    }

#[Route('/Recruteur/ajouter', name: 'app_annonce_ajouter')]
    public function ajouterRecruteur(Request $request, EntityManagerInterface $entityManager): Response
    {
        $Recruteur = new Recruteur();

    $form = $this->createForm(RecruteurType::class, $Recruteur);
    $form->handleRequest($request);

   if ($form->isSubmitted() && $form->isValid()) {
    $entityManager->persist($Recruteur);
    $entityManager->flush();
}

 return $this->render('Recruteur/ajout.html.twig', [
    'form' => $form->createView(),
 ]);
    }
    
}
