<?php

namespace App\Controller;

use App\Entity\Chercheur;
use App\Entity\Recruteur;
use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Psr\Log\LoggerInterface;

class RegistrationController extends AbstractController
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            // Check if form is valid
            if (!$form->isValid()) {
                // Form has validation errors - they will be displayed by template
                return $this->render('registration/register.html.twig', [
                    'registrationForm' => $form->createView(),
                ]);
            }

            try {
                // Get form data
                $email = $form->get('email')->getData();
                $nom = $form->get('nom')->getData();
                $prenom = $form->get('prenom')->getData();
                $plainPassword = $form->get('plainPassword')->getData();
                $role = $form->get('role')->getData();

                // Validate role
                if (!in_array($role, ['ROLE_CHERCHEUR', 'ROLE_RECRUTEUR'])) {
                    throw new \Exception('Rôle invalide');
                }

                // Set user properties
                $user->setEmail($email);
                $user->setNom($nom);
                $user->setPrenom($prenom);
                $user->setRoles([$role]);
                
                // Hash password
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);

                // Persist user
                $entityManager->persist($user);

                // Create associated profile based on role
                if ($role === 'ROLE_CHERCHEUR') {
                    $chercheur = new Chercheur();
                    $chercheur->setUser($user);
                    $entityManager->persist($chercheur);
                } elseif ($role === 'ROLE_RECRUTEUR') {
                    $recruteur = new Recruteur();
                    $recruteur->setUser($user);
                    $entityManager->persist($recruteur);
                }

                // Flush to database
                $entityManager->flush();

                // Success - redirect to login
                $this->addFlash('success', 'Inscription réussie! Vous pouvez maintenant vous connecter.');
                return $this->redirectToRoute('app_login');

            } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
                $this->logger->error('Registration error - duplicate email: ' . $email);
                $this->addFlash('error', 'Cette adresse email est déjà utilisée.');
                
            } catch (\Exception $e) {
                $this->logger->error('Registration error: ' . $e->getMessage(), [
                    'exception' => $e,
                    'email' => $email ?? 'unknown'
                ]);
                $this->addFlash('error', 'Une erreur est survenue lors de l\'inscription: ' . $e->getMessage());
            }
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
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
