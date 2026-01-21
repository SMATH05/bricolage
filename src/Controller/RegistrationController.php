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
use Psr\Log\LoggerInterface;


class RegistrationController extends AbstractController
{
    public function __construct(private EmailVerifier $emailVerifier, private LoggerInterface $logger)
    {
    }
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if (!$form->isValid()) {
                // Log form errors for debugging
                $errors = [];
                foreach ($form->getErrors(true) as $error) {
                    $errors[] = $error->getMessage();
                }
                // Form will be re-rendered with errors displayed
            } else {
                try {
                    $user->setEmail($form->get('email')->getData());
                    $user->setNom($form->get('nom')->getData());
                    $user->setPrenom($form->get('prenom')->getData());
                    $user->setPassword(
                        $passwordHasher->hashPassword(
                            $user,
                            trim($form->get('plainPassword')->getData())
                        )
                    );

                    $selectedRole = $form->get('roles')->getData();

                    // Security lockdown: Only allow Researcher or Recruiter registration
                    if (!in_array($selectedRole, ['ROLE_CHERCHEUR', 'ROLE_RECRUTEUR'])) {
                        $this->addFlash('error', 'Role non autorisé.');
                        return $this->redirectToRoute('app_register');
                    }

                    $user->setRoles([$selectedRole]);

                    // Create profile based on role
                    if ($selectedRole === 'ROLE_CHERCHEUR') {
                        $chercheur = new Chercheur();
                        $chercheur->setUser($user);
                        $entityManager->persist($chercheur);
                    } elseif ($selectedRole === 'ROLE_RECRUTEUR') {
                        $recruteur = new Recruteur();
                        $recruteur->setUser($user);
                        $entityManager->persist($recruteur);
                    }

                    $entityManager->persist($user);
                    $entityManager->flush();

                    $this->addFlash('success', 'Inscription réussie! Bienvenue sur Bricolage.');

                    return match ($selectedRole) {
                        'ROLE_CHERCHEUR' => $this->redirectToRoute('app_annonce'),
                        'ROLE_RECRUTEUR' => $this->redirectToRoute('app_annonce'),
                        default => $this->redirectToRoute('app_home'),
                    };
                } catch (\Exception $e) {
                    $this->logger->error('Registration error: ' . $e->getMessage(), [
                        'exception' => $e,
                        'email' => $form->get('email')->getData() ?? 'unknown'
                    ]);
                    $this->addFlash('error', 'Une erreur est survenue lors de l\'inscription. Veuillez réessayer.');
                }
            }
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
