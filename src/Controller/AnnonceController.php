<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Form\AnnonceType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\AnnonceRepository;



final class AnnonceController extends AbstractController
{
     #[Route('/home', name: 'app_home')]
    public function index(): Response
    {
       return $this->render('home/index.html.twig', [
            'controller_name' => 'AnnonceController',
            ]);
    }

    #[Route('/recruteur/annonce', name: 'app_annonce')]
    public function home(AnnonceRepository $annonceRepository): Response
    {
        $annonces = $annonceRepository->findAll();

        return $this->render('annonce/index.html.twig', [
            'annonces' => $annonces,
            ]);
    }
 #[Route('/recruteur/annonce/ajouter', name: 'app_annonces_ajouter')]
    public function ajouterAnnonce(Request $request, EntityManagerInterface $entityManager): Response
    {
        $annonce = new Annonce();

    $form = $this->createForm(AnnonceType::class, $annonce);
    $form->handleRequest($request);

   if ($form->isSubmitted() && $form->isValid()) {
    $entityManager->persist($annonce);
    $entityManager->flush();
 return $this->redirectToRoute('app_annonce');
 }

 return $this->render('annonce/ajout.html.twig', [
    'form' => $form->createView(),
 ]);
    }
  
    
    #[Route('/recruteur/annonce/modifier/{id}', name: 'app_annonce_modifier')]
    public function modifierAnnonce($id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $annonce = $entityManager->getRepository(Annonce::class)->find($id);

        if (!$annonce) {
            throw $this->createNotFoundException('Annonce introuvable');
        }

        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_annonce');
        }

        return $this->render('annonce/modifier.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/recruteur/annonce/supprimer/{id}', name: 'app_annonce_supprimer')]
public function supprimerAnnonce(
    int $id,
    Request $request,
    EntityManagerInterface $entityManager
): Response {
    $annonce = $entityManager->getRepository(Annonce::class)->find($id);

    if (!$annonce) {
        throw $this->createNotFoundException("Annonce introuvable");
    }

    // Récupérer les candidatures liées
    $candidatures = $annonce->getCandidatures();

    // Vérifier si l'utilisateur a confirmé
    if ($request->query->get('confirm') !== 'true') {
        return $this->render('annonce/confirm_delete.html.twig', [
            'annonce' => $annonce,
            'candidatures' => $candidatures,
        ]);
    }

    // Si confirmé → supprimer candidatures puis annonce
    foreach ($candidatures as $candidature) {
        $entityManager->remove($candidature);
    }

    $entityManager->remove($annonce);
    $entityManager->flush();

    return $this->redirectToRoute('app_annonce');
}
}