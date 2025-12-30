<?php

namespace App\Controller;


use App\Entity\Chercheur;
use App\Form\ChercheurType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
<<<<<<< HEAD
use Symfony\Component\Routing\Annotation\Route;
=======
use Symfony\Component\Routing\Attribute\Route;
>>>>>>> dc7c53a880c5b7e5df5fcb6190304ee94ef06aa2





final class ChercheurController extends AbstractController
{
    #[Route('/chercheur', name: 'app_chercheur')]
    public function index(): Response
    {
        return $this->render('chercheur/index.html.twig', [
            'controller_name' => 'ChercheurController',
        ]);
    }


   #[Route('/ajouter', name: 'app_chercheur_ajouter')]
    public function ajouterChercheur(Request $request, EntityManagerInterface $entityManager): Response
    {
        $chercheur = new Chercheur();

    $form = $this->createForm(ChercheurType::class, $chercheur);
    $form->handleRequest($request);

   if ($form->isSubmitted() && $form->isValid()) {
    $entityManager->persist($chercheur);
    $entityManager->flush();
}

 return $this->render('/ajouter.html.twig', [
    'form' => $form->createView(),
 ]);
    }
    

    
}
