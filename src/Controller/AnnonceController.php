<?php

namespace App\Controller;
use App\Form\AnnonceType;
use App\Entity\Annonce;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AnnonceController extends AbstractController
{
    #[Route('/Annonce', name: 'app_annonce')]
    public function index(): Response
    {
        return $this->render('annonce/index.html.twig', [
            'controller_name' => 'AnnonceController',
        ]);

    }


    #[Route('/recruteur/ajouter', name: 'app_annonce_ajouter')]
    public function ajouterAnonce(Request $request, EntityManagerInterface $entityManager): Response
    {
        $annonce = new Annonce();

    $form = $this->createForm(AnnonceType::class, $annonce);
    $form->handleRequest($request);

   if ($form->isSubmitted() && $form->isValid()) {
    $entityManager->persist($annonce);
    $entityManager->flush();
}

 return $this->render('annonce/ajout.html.twig', [
    'form' => $form->createView(),
 ]);
    }

    

}