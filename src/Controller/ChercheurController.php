<?php

namespace App\Controller;


use App\Entity\Chercheur;
use App\Form\ChercheurType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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


   #[Route('/chercheur/ajouter', name: 'app_chercheur_ajouter')]
    public function ajouterChercheur(Request $request, EntityManagerInterface $entityManager): Response
    {
        $chercheur = new Chercheur();

    $form = $this->createForm(ChercheurType::class, $chercheur);
    $form->handleRequest($request);

   if ($form->isSubmitted() && $form->isValid()) {
    $entityManager->persist($chercheur);
    $entityManager->flush();
}

 return $this->render('chercheur/ajouter.html.twig', [
    'form' => $form->createView(),
 ]);
    }
    

    
}
