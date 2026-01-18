<?php

namespace App\Controller;

use App\Entity\Chercheur;
use App\Entity\Recruteur;
use App\Form\ChercheurType;
use App\Form\RecruteurType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[IsGranted('ROLE_USER')]
class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $profile = null;
        $form = null;

        if ($this->isGranted('ROLE_CHERCHEUR')) {
            $profile = $user->getChercheur();
            if (!$profile) {
                $profile = new Chercheur();
                $profile->setUser($user);
                $profile->setNom($user->getUserIdentifier());
                $profile->setEmail($user->getEmail());
                $profile->setIdChercheur(uniqid('CH_'));
                $profile->setMotDePasse('dummy');
                $entityManager->persist($profile);
                $entityManager->flush();
            }
            $form = $this->createForm(ChercheurType::class, $profile);
        } elseif ($this->isGranted('ROLE_RECRUTEUR')) {
            $profile = $user->getRecruteur();
            if (!$profile) {
                $profile = new Recruteur();
                $profile->setUser($user);
                $profile->setNom($user->getUserIdentifier());
                $profile->setEmail($user->getEmail());
                $profile->setPassword('dummy');
                $entityManager->persist($profile);
                $entityManager->flush();
            }
            $form = $this->createForm(RecruteurType::class, $profile);
        }

        if ($form) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $photoFile = $form->get('photo')->getData();

                if ($photoFile) {
                    $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $photoFile->guessExtension();

                    try {
                        $photoFile->move(
                            $this->getParameter('profiles_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // ... handle exception
                    }

                    $profile->setPhoto($newFilename);
                }

                $entityManager->flush();
                $this->addFlash('success', 'Profil mis à jour.');
                return $this->redirectToRoute('app_profile');
            }
        }

        return $this->render('profile/index.html.twig', [
            'profile' => $profile,
            'form' => $form ? $form->createView() : null,
        ]);
    }
}
