<?php

namespace App\Controller;

use App\Entity\Chercheur;
use App\Entity\Recruteur;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;


class RegistrationController extends AbstractController
{
    public function __construct(private EmailVerifier $emailVerifier)
    {
    }
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $selectedRole = $form->get('roles')->getData();
            $user->setRoles([$selectedRole]);

            $user->setIsVerified(true);

            $entityManager->persist($user);

            $nom = $form->get('nom')->getData();
            $prenom = $form->get('prenom')->getData();

            if ($selectedRole === 'ROLE_CHERCHEUR') {
                $chercheur = new Chercheur();
                $chercheur->setUser($user);
                // Set values from form
                $chercheur->setNom($nom);
                $chercheur->setPrenom($prenom);
                $chercheur->setEmail($user->getEmail());
                $chercheur->setIdChercheur(uniqid('CH_'));
                $chercheur->setDescription('Description...');
                $chercheur->setDisponibilite('Disponible');
                $chercheur->setMotDePasse('dummy');
                $entityManager->persist($chercheur);
            } elseif ($selectedRole === 'ROLE_RECRUTEUR') {
                $recruteur = new Recruteur();
                $recruteur->setUser($user);
                $recruteur->setNom($nom . ' ' . $prenom);
                $recruteur->setEmail($user->getEmail());
                $recruteur->setTelephone('0000000000');
                $recruteur->setPassword('dummy');
                $entityManager->persist($recruteur);
            }

            $entityManager->flush();

            return match ($selectedRole) {
                'ROLE_ADMIN' => $this->redirectToRoute('app_admin'),
                'ROLE_CHERCHEUR' => $this->redirectToRoute('app_annonce'), // Go to ads list
                'ROLE_RECRUTEUR' => $this->redirectToRoute('app_annonce'), // Redirect to ads list
                default => $this->redirectToRoute('app_home'),
            };
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }







    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            /** @var User $user */
            $user = $this->getUser();
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_register');
    }
}
