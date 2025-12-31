<?php

namespace App\Controller;
use App\Form\AnnonceType;
use App\Entity\Annonce;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\AnnonceRepository;



final class AnnonceController extends AbstractController
{
    #[Route('/recruteur/annonce', name: 'app_annonces')]
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
 return $this->redirectToRoute('app_annonces');
}

 return $this->render('annonce/ajout.html.twig', [
    'form' => $form->createView(),
 ]);
    }

 #[Route('/recruteur/annonce/supprimer/{id}', name: 'app_supprimer')]
  public function supprimerChercheur( $id, EntityManagerInterface $entityManager): Response
{

$annonce = $entityManager->getRepository(Annonce::class)->find($id);
  if ($annonce) {
            $entityManager->remove($annonce);
            $entityManager->flush();
        }
return $this->redirectToRoute('app_annonces');

    }
}