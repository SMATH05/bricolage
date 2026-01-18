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
use App\Repository\CandidatureRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

final class AnnonceController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(AnnonceRepository $annonceRepository): Response
    {
        if ($this->getUser()) {
            if ($this->isGranted('ROLE_ADMIN')) {
                return $this->redirectToRoute('app_admin');
            }
            return $this->redirectToRoute('app_annonce');
        }

        $recentAnnonces = $annonceRepository->findBy([], ['date_publication' => 'DESC'], 3);

        return $this->render('home/index.html.twig', [
            'recentAnnonces' => $recentAnnonces,
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/annonces', name: 'app_annonce')]
    public function home(AnnonceRepository $annonceRepository): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if ($this->isGranted('ROLE_ADMIN')) {
            $annonces = $annonceRepository->findBy([], ['date_publication' => 'DESC']);
        } elseif ($this->isGranted('ROLE_RECRUTEUR')) {
            $recruteur = $user->getRecruteur();
            $annonces = $annonceRepository->findBy(['recrut_id' => $recruteur], ['date_publication' => 'DESC']);
        } else {
            $annonces = $annonceRepository->findBy([], ['date_publication' => 'DESC']);
        }

        return $this->render('annonce/index.html.twig', [
            'annonces' => $annonces,
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/recruteur/annonce/ajouter', name: 'app_annonces_ajouter')]
    public function ajouterAnnonce(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        if (!$this->isGranted('ROLE_RECRUTEUR') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $annonce = new Annonce();
        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photoFile = $form->get('photo')->getData();

            if ($photoFile) {
                $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $photoFile->guessExtension();

                try {
                    $photoFile->move(
                        $this->getParameter('annonces_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }

                $annonce->setPhoto($newFilename);
            }

            /** @var \App\Entity\User $user */
            $user = $this->getUser();
            if ($this->isGranted('ROLE_RECRUTEUR')) {
                $annonce->setRecrutId($user->getRecruteur());
            }

            $entityManager->persist($annonce);
            $entityManager->flush();
            return $this->redirectToRoute('app_annonce');
        }

        return $this->render('annonce/ajout.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/recruteur/annonce/modifier/{id}', name: 'app_annonce_modifier')]
    public function modifierAnnonce($id, Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $annonce = $entityManager->getRepository(Annonce::class)->find($id);

        if (!$annonce) {
            throw $this->createNotFoundException('Annonce introuvable');
        }

        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        if ($annonce->getRecrutId() !== $user->getRecruteur() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException("Vous n'êtes pas autorisé à modifier cette annonce.");
        }

        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photoFile = $form->get('photo')->getData();

            if ($photoFile) {
                $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $photoFile->guessExtension();

                try {
                    $photoFile->move(
                        $this->getParameter('annonces_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }

                $annonce->setPhoto($newFilename);
            }

            $entityManager->flush();
            return $this->redirectToRoute('app_annonce');
        }

        return $this->render('annonce/modifier.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/recruteur/annonce/supprimer/{id}', name: 'app_annonce_supprimer')]
    public function supprimerAnnonce(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $annonce = $entityManager->getRepository(Annonce::class)->find($id);

        if (!$annonce) {
            throw $this->createNotFoundException("Annonce introuvable");
        }

        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        if ($annonce->getRecrutId() !== $user->getRecruteur() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException("Vous n'êtes pas autorisé à supprimer cette annonce.");
        }

        $candidatures = $annonce->getCandidatures();

        if ($request->query->get('confirm') !== 'true') {
            return $this->render('annonce/confirm_delete.html.twig', [
                'annonce' => $annonce,
                'candidatures' => $candidatures,
            ]);
        }

        foreach ($candidatures as $candidature) {
            $entityManager->remove($candidature);
        }

        $entityManager->remove($annonce);
        $entityManager->flush();

        return $this->redirectToRoute('app_annonce');
    }

    #[IsGranted('ROLE_RECRUTEUR')]
    #[Route('/recruteur/candidatures', name: 'app_recruteur_candidatures')]
    public function candidatures(CandidatureRepository $candidatureRepository): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $recruteur = $user->getRecruteur();

        $candidatures = $candidatureRepository->findForRecruteur($recruteur);

        return $this->render('annonce/candidatures.html.twig', [
            'candidatures' => $candidatures,
        ]);
    }

    #[IsGranted('ROLE_RECRUTEUR')]
    #[Route('/recruteur/candidature/statut/{id}/{statut}', name: 'app_recruteur_candidature_statut')]
    public function modifierCandidatureStatut(int $id, string $statut, EntityManagerInterface $entityManager): Response
    {
        $candidature = $entityManager->getRepository(\App\Entity\Candidature::class)->find($id);

        if (!$candidature) {
            throw $this->createNotFoundException('Candidature introuvable');
        }

        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        if ($candidature->getAnnonceId()->getRecrutId() !== $user->getRecruteur() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $candidature->setStatut($statut);
        $entityManager->flush();

        $this->addFlash('success', 'Statut de la candidature mis à jour.');
        return $this->redirectToRoute('app_recruteur_candidatures');
    }
}