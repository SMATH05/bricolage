<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class ForgotPasswordController extends AbstractController
{
    #[Route('/forgot-password', name: 'app_forgot_password')]
    public function forgotPassword(Request $request, MailerInterface $mailer, EntityManagerInterface $em): Response
    {
        $form = $this->createFormBuilder()
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => [
                    'placeholder' => 'Entrez votre adresse email',
                    'class' => 'form-control'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Envoyer le lien de réinitialisation',
                'attr' => [
                    'class' => 'btn btn-primary w-100'
                ]
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);

            if ($user) {
                // Générer un token de réinitialisation
                $token = bin2hex(random_bytes(32));
                $user->setResetToken($token);
                $user->setResetTokenExpiresAt(new \DateTime('+1 hour'));
                $em->flush();

                // Envoyer l'email
                $email = (new Email())
                    ->from('noreply@bricolage.com')
                    ->to($user->getEmail())
                    ->subject('Réinitialisation de votre mot de passe')
                    ->html($this->renderView('emails/forgot_password.html.twig', [
                        'user' => $user,
                        'token' => $token
                    ]));

                try {
                    $mailer->send($email);
                    $this->addFlash('success', 'Un email de réinitialisation a été envoyé à votre adresse email.');
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors de l\'envoi de l\'email. Veuillez réessayer.');
                }
            } else {
                $message = 'Si un compte avec cette adresse email existe, un email de réinitialisation a été envoyé.';
                $this->addFlash('info', $message);
            }

            return $this->redirectToRoute('app_forgot_password');
        }

        return $this->render('security/forgot_password.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/reset-password/{token}', name: 'app_reset_password')]
    public function resetPassword(string $token, Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $em->getRepository(User::class)->findOneBy(['resetToken' => $token]);

        if (!$user || $user->getResetTokenExpiresAt() < new \DateTime()) {
            $this->addFlash('error', 'Ce lien de réinitialisation est invalide ou a expiré.');
            return $this->redirectToRoute('app_forgot_password');
        }

        $form = $this->createFormBuilder()
            ->add('password', \Symfony\Component\Form\Extension\Core\Type\PasswordType::class, [
                'label' => 'Nouveau mot de passe',
                'attr' => [
                    'placeholder' => 'Entrez votre nouveau mot de passe',
                    'class' => 'form-control'
                ]
            ])
            ->add('confirm_password', \Symfony\Component\Form\Extension\Core\Type\PasswordType::class, [
                'label' => 'Confirmer le mot de passe',
                'attr' => [
                    'placeholder' => 'Confirmez votre nouveau mot de passe',
                    'class' => 'form-control'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Réinitialiser le mot de passe',
                'attr' => [
                    'class' => 'btn btn-primary w-100'
                ]
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            
            if ($data['password'] !== $data['confirm_password']) {
                $this->addFlash('error', 'Les mots de passe ne correspondent pas.');
                return $this->render('security/reset_password.html.twig', [
                    'form' => $form->createView(),
                    'token' => $token
                ]);
            }

            // Hasher le nouveau mot de passe
            $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
            $user->setPassword($hashedPassword);
            
            // Nettoyer le token
            $user->setResetToken(null);
            $user->setResetTokenExpiresAt(null);
            
            $em->flush();

            $this->addFlash('success', 'Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/reset_password.html.twig', [
            'form' => $form->createView(),
            'token' => $token
        ]);
    }
}
