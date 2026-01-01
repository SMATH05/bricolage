<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Candidature;
use App\Form\CandidatureType;
use App\Repository\CandidatureRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CandidatureController extends AbstractController
{
    #[Route('/chercheur/Candidature', name: 'app_candidature')]
    public function home(CandidatureRepository $candidatureRepository): Response
    {
        $candidatures = $candidatureRepository->findAll();

        return $this->render('candidature/index.html.twig', [
            'candidatures' => $candidatures,
        ]);
    }
    #[Route('/chercheur/Candidature/ajouter', name: 'app_candidature_ajouter')]
    public function ajouterCandidature(Request $request, EntityManagerInterface $entityManager): Response
    {
        $candidature = new Candidature();

    $form = $this->createForm(CandidatureType::class, $candidature);
    $form->handleRequest($request);

   if ($form->isSubmitted() && $form->isValid()) {
    $entityManager->persist($candidature);
    $entityManager->flush();
     return $this->redirectToRoute('app_candidature');
}

 return $this->render('candidature/ajouterCandidature.html.twig', [
    'form' => $form->createView(),
 ]);
    }
    
 #[Route('/chercheur/supprimer/{id}', name: 'app_supprimer')]
  public function supprimerChercheur( $id, EntityManagerInterface $entityManager): Response
{

$candidature = $entityManager->getRepository(Candidature::class)->find($id);
  if ($candidature) {
            $entityManager->remove($candidature);
            $entityManager->flush();
        }
return $this->redirectToRoute('app_candidature');

    }
    #[Route('/condidateur/modifier/{id}', name: 'app_Condidature_modifier')]
    public function modifierCondidature($id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $condidature = $entityManager->getRepository(condidateur::class)->find($id);

        if (!$condidateur) {
            throw $this->createNotFoundException('condidateur introuvable');
        }

        $form = $this->createForm(condidateurType::class, $condidateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_condidateur');
        }

        return $this->render('condidateur/modifier.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
