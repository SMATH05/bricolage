<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(
        \App\Repository\ChercheurRepository $chercheurRepo,
        \App\Repository\RecruteurRepository $recruteurRepo,
        \App\Repository\AnnonceRepository $annonceRepo
    ): Response {
        return $this->render('admin/index.html.twig', [
            'totalChercheurs' => $chercheurRepo->count([]),
            'totalRecruteurs' => $recruteurRepo->count([]),
            'totalAnnonces' => $annonceRepo->count([]),
            'recentChercheurs' => $chercheurRepo->findBy([], ['id' => 'DESC'], 5),
            'recentRecruteurs' => $recruteurRepo->findBy([], ['id' => 'DESC'], 5),
        ]);
    }

    #[Route('/setup-one-time-admin-initial', name: 'app_admin_setup')]
    public function setupAdmin(
        \Doctrine\ORM\EntityManagerInterface $em,
        \Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface $hasher
    ): Response {
        $email = 'admin@bricolage.com';
        $existing = $em->getRepository(\App\Entity\User::class)->findOneBy(['email' => $email]);

        if ($existing) {
            return new Response('Admin account already exists.');
        }

        $user = new \App\Entity\User();
        $user->setEmail($email);
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword($hasher->hashPassword($user, 'adminPassword123!'));
        $user->setIsVerified(true);

        $em->persist($user);
        $em->flush();

        return new Response('Admin account created: ' . $email . ' (Password: adminPassword123!) - PLEASE DELETE THIS ROUTE AFTER USE.');
    }
}
