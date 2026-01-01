<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Recruteur;
use App\Form\RecruteurType;
use App\Repository\RecruteurRepository;

final class RecruteurController extends AbstractController
{
    #[Route('/recruteur', name: 'app_recruteur')]
    public function home(RecruteurRepository $recruteurRepository): Response
    {
        $recruteurs = $recruteurRepository->findAll();

        return $this->render('recruteur/index.html.twig', [
            'recruteurs' => $recruteurs,
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

    #[Route('/recruteur/{id}', name: 'app_recruteur_supprimer', methods: ['GET'])]
    public function supprimerRecruteur($id, EntityManagerInterface $entityManager): Response
    {
        $recruteur = $entityManager->getRepository(Recruteur::class)->find($id);
        
        if ($recruteur) {
            $entityManager->remove($recruteur);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_recruteur');
    }
    #[Route('/recruteur/modifier/{id}', name: 'app_recruteur_modifier')]
    public function modifierrecruteur($id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $recruteur = $entityManager->getRepository(recruteur::class)->find($id);

        if (!$recruteur) {
            throw $this->createNotFoundException('recruteur introuvable');
        }

        $form = $this->createForm(recruteurType::class, $recruteur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_recruteur');
        }

        return $this->render('recruteur/modifier.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
