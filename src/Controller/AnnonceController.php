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
use App\Repository\RecruteurRepository;

final class AnnonceController extends AbstractController
{
    #[Route('/Annonce', name: 'app_annonce')]
    public function index(AnnonceRepository $annonceRepository): Response
    {
        $annonces = $annonceRepository->findAll();
       
        return $this->render('annonce/indexAnnonce.html.twig', [
          
            'annonces' => $annonces,
        ]);
    }


       #[Route('/Recruteur/ajouter', name: 'app_annonce_ajouter')]
    public function ajouterAnonce(Request $request, EntityManagerInterface $entityManager): Response
    {
        $annonce = new Annonce();

    $form = $this->createForm(AnnonceType::class, $annonce);
    $form->handleRequest($request);

   if ($form->isSubmitted() && $form->isValid()) {
    $entityManager->persist($annonce);
    $entityManager->flush();
}

 return $this->render('annonce/ajout.html.twig', [
    'form' => $form->createView(),
 ]);
    }


 #[Route('/annonce/supprimer/{id}', name: 'app_supprimer_annonce')]
public function supprimer_annonce($id, EntityManagerInterface $entityManager): Response{
$annonce=$entityManager->getRepository(Annonce::class)->find($id);
if($annonce){
    $entityManager->remove($annonce);
    $entityManager->flush();


}
return $this->redirectToRoute('app_annonce');


    

}
}