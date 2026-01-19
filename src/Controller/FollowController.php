<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class FollowController extends AbstractController
{
    #[Route('/follow/{id}', name: 'app_follow')]
    public function follow(User $userToFollow, EntityManagerInterface $em): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if ($currentUser === $userToFollow) {
            $this->addFlash('error', 'Vous ne pouvez pas vous suivre vous-même.');
            return $this->redirectToRoute('app_home');
        }

        $currentUser->follow($userToFollow);
        $em->flush();

        $this->addFlash('success', sprintf('Vous suivez maintenant %s', $userToFollow->getUserIdentifier()));

        return $this->redirect($this->generateUrl('app_home')); // Fallback
    }

    #[Route('/unfollow/{id}', name: 'app_unfollow')]
    public function unfollow(User $userToUnfollow, EntityManagerInterface $em): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        $currentUser->unfollow($userToUnfollow);
        $em->flush();

        $this->addFlash('success', sprintf('Vous ne suivez plus %s', $userToUnfollow->getUserIdentifier()));

        return $this->redirect($this->generateUrl('app_home')); // Fallback
    }
}
