<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
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
        \Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface $hasher,
        Request $request
    ): Response {
        $email = 'admin@bricolage.com';
        $force = $request->query->get('force') === '1';
        $existing = $em->getRepository(\App\Entity\User::class)->findOneBy(['email' => $email]);

        if ($existing && $force) {
            $em->remove($existing);
            $em->flush();
            $existing = null;
        }

        if ($existing) {
            return new Response('Admin account already exists. Use ?force=1 to reset it. Email: ' . $existing->getEmail() . ' Roles: ' . implode(', ', $existing->getRoles()));
        }

        $user = new \App\Entity\User();
        $user->setEmail($email);
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword($hasher->hashPassword($user, 'adminPassword123!'));
        $user->setIsVerified(true);

        $em->persist($user);
        $em->flush();

        return new Response('SUCCESS: Admin account created.<br>Email: <b>' . $email . '</b><br>Password: <b>adminPassword123!</b><br><br>Please visit /login to sign in.');
    }
}
