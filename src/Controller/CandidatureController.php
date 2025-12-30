<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Candidature;
use App\Form\CandidatureType;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CandidatureController extends AbstractController
{
    #[Route('/candidature', name: 'app_candidature')]
    public function index(): Response
    {
        return $this->render('candidature/index.html.twig', [
            'controller_name' => 'CandidatureController',
        ]);
    }



    #[Route('/Candidature/ajouter', name: 'app_candidature_ajouter')]
    public function ajouterCandidature(Request $request, EntityManagerInterface $entityManager): Response
    {
        $candidature = new Candidature();

    $form = $this->createForm(CandidatureType::class, $candidature);
    $form->handleRequest($request);

   if ($form->isSubmitted() && $form->isValid()) {
    $entityManager->persist($candidature);
    $entityManager->flush();
}

 return $this->render('candidature/ajouterCandidature.html.twig', [
    'form' => $form->createView(),
 ]);
    }
    
}
