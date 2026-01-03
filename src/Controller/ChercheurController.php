<?php

namespace App\Controller;


use App\Entity\Chercheur;
use App\Form\ChercheurType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Repository\ChercheurRepository;

 #[IsGranted('ROLE_CHERCHEUR')]
final class ChercheurController extends AbstractController
{

    #[Route('/chercheur', name: 'app_chercheur')]
   
    public function home(ChercheurRepository $chercheurRepository): Response
    {
        $chercheurs = $chercheurRepository->findAll();

        return $this->render('chercheur/index.html.twig', [
            'chercheurs' => $chercheurs,
        ]);
    }
   
   #[Route('/chercheur/ajouter', name: 'app_chercheur_ajouter')]
    #[IsGranted('ROLE_CHERCHEUR')]
    public function ajouterChercheur(Request $request, EntityManagerInterface $entityManager): Response
    {
        $chercheur = new Chercheur();

    $form = $this->createForm(ChercheurType::class, $chercheur);
    $form->handleRequest($request);

   if ($form->isSubmitted() && $form->isValid()) {
    $entityManager->persist($chercheur);
    $entityManager->flush();

 

    return $this->redirectToRoute('app_chercheur');
     }

  return $this->render('chercheur/ajouter.html.twig', [
    'form' => $form->createView(),
      ]);
    }

    #[Route('/chercheur/supprimer/{id}', name: 'app_supprimerchercheur')]
    #[IsGranted('ROLE_CHERCHEUR')]
  public function supprimerChercheur( $id, EntityManagerInterface $entityManager): Response
{

$chercheur = $entityManager->getRepository(Chercheur::class)->find($id);
  if ($chercheur) {
            $entityManager->remove($chercheur);
            $entityManager->flush();
        }
return $this->redirectToRoute('app_chercheur');

} 
#[Route('/chercheur/modifier/{id}', name: 'app_chercheur_modifier')]
    #[IsGranted('ROLE_CHERCHEUR')]
    public function modifierchercheur($id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $chercheur = $entityManager->getRepository(Chercheur::class)->find($id);

        if (!$chercheur) {
            throw $this->createNotFoundException('chercheur introuvable');
        }

        $form = $this->createForm(chercheurType::class, $chercheur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_chercheur');
        }

        return $this->render('chercheur/modifier.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    
}
