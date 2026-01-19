<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Candidature;
use App\Form\CandidatureType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Annonce;
use App\Repository\CandidatureRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;
#[IsGranted('ROLE_CHERCHEUR')]
final class CandidatureController extends AbstractController
{
    #[Route('/chercheur/Candidature', name: 'app_candidature')]
    public function home(CandidatureRepository $candidatureRepository): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $chercheur = $user->getChercheur();
        $candidatures = $candidatureRepository->findBy(['chercheur_id' => $chercheur]);

        return $this->render('candidature/index.html.twig', [
            'candidatures' => $candidatures,
        ]);
    }
    #[Route('/chercheur/Candidature/ajouter/{annonce_id}', name: 'app_candidature_ajouter')]
    public function ajouterCandidature(int $annonce_id, Request $request, EntityManagerInterface $entityManager, CandidatureRepository $candidatureRepository): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $chercheur = $user->getChercheur();
        if (!$chercheur) {
            $this->addFlash('danger', 'Votre profil chercheur est incomplet.');
            return $this->redirectToRoute('app_profile');
        }

        $annonce = $entityManager->getRepository(Annonce::class)->find($annonce_id);

        if (!$annonce) {
            throw $this->createNotFoundException('Annonce introuvable');
        }

        // Check if already applied
        $existing = $candidatureRepository->findOneBy([
            'chercheur_id' => $chercheur,
            'annonce_id' => $annonce
        ]);

        if ($existing) {
            $this->addFlash('warning', 'Vous avez déjà postulé à cette annonce.');
            return $this->redirectToRoute('app_annonce');
        }

        try {
            $candidature = new Candidature();
            $candidature->setChercheurId($chercheur);
            $candidature->setAnnonceId($annonce);
            $candidature->setDatePro(new \DateTime());
            $candidature->setStatut('En attente');
            $candidature->setIdCandidature(uniqid('CAN_'));

            $entityManager->persist($candidature);
            $entityManager->flush();

            $this->addFlash('success', 'Votre candidature a été envoyée avec succès.');
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Erreur lors de la candidature : ' . $e->getMessage());
        }

        return $this->redirectToRoute('app_candidature');
    }

    #[Route('/chercheur/candidature/supprimer/{id}', name: 'app_supprimercandidature')]
    public function supprimerCandidature($id, EntityManagerInterface $entityManager): Response
    {

        $candidature = $entityManager->getRepository(Candidature::class)->find($id);
        if ($candidature) {
            $entityManager->remove($candidature);
            $entityManager->flush();
        }
        return $this->redirectToRoute('app_candidature');

    }

    #[Route('/chercheur/candidature/modifier/{id}', name: 'app_Candidature_modifier')]
    public function modifierCandidature($id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $candidature = $entityManager->getRepository(Candidature::class)->find($id);

        if (!$candidature) {
            throw $this->createNotFoundException('candidature introuvable');
        }

        $form = $this->createForm(CandidatureType::class, $candidature);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_candidature');
        }

        return $this->render('candidature/modifier.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
