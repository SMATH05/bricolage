<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Form\AnnonceType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AnnonceController extends AbstractController
{
    // ✅ Afficher toutes les annonces
    #[Route('/annonce', name: 'app_annonce')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $annonces = $entityManager->getRepository(Annonce::class)->findAll();

        return $this->render('annonce/index.html.twig', [
            'annonces' => $annonces,
        ]);
    }

    // ✅ Ajouter une annonce
    #[Route('/annonce/ajouter', name: 'app_annonce_ajouter')]
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

        return $this->render('annonce/ajouter.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // ✅ Modifier une annonce
    #[Route('/annonce/modifier/{id}', name: 'app_annonce_modifier')]
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

    // ✅ Supprimer une annonce
    #[Route('/annonce/supprimer/{id}', name: 'app_annonce_supprimer')]
    public function supprimerAnnonce($id, EntityManagerInterface $entityManager): Response
    {
        $annonce = $entityManager->getRepository(Annonce::class)->find($id);

        if ($annonce) {
            $entityManager->remove($annonce);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_annonce');
    }
}