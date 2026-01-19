<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\User;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class ChatController extends AbstractController
{
    #[Route('/chat', name: 'app_chat')]
    public function index(MessageRepository $messageRepo, UserRepository $userRepo): Response
    {
        $user = $this->getUser();
        $conversations = $messageRepo->findLastMessages($user);

        $usersWithMessages = [];
        foreach ($conversations as $conv) {
            $otherUser = $userRepo->find($conv['other_user_id']);
            if ($otherUser) {
                // Get last message for preview
                $lastMsg = $messageRepo->findOneBy(
                    ['sender' => [$user, $otherUser], 'recipient' => [$user, $otherUser]],
                    ['createdAt' => 'DESC']
                );
                $usersWithMessages[] = [
                    'user' => $otherUser,
                    'lastMessage' => $lastMsg
                ];
            }
        }

        return $this->render('chat/index.html.twig', [
            'conversations' => $usersWithMessages,
            'activeUser' => null
        ]);
    }

    #[Route('/chat/{id}', name: 'app_chat_conversation')]
    public function conversation(User $otherUser, MessageRepository $messageRepo, UserRepository $userRepo, EntityManagerInterface $em): Response
    {
        $currentUser = $this->getUser();
        if ($currentUser === $otherUser) {
            return $this->redirectToRoute('app_chat');
        }

        $messages = $messageRepo->findConversation($currentUser, $otherUser);

        // Mark as read
        foreach ($messages as $msg) {
            if ($msg->getRecipient() === $currentUser && !$msg->isRead()) {
                $msg->setIsRead(true);
            }
        }
        $em->flush();

        $conversations = $messageRepo->findLastMessages($currentUser);
        $usersWithMessages = [];
        foreach ($conversations as $conv) {
            $u = $userRepo->find($conv['other_user_id']);
            if ($u) {
                $lastMsg = $messageRepo->findOneBy(
                    ['sender' => [$currentUser, $u], 'recipient' => [$currentUser, $u]],
                    ['createdAt' => 'DESC']
                );
                $usersWithMessages[] = [
                    'user' => $u,
                    'lastMessage' => $lastMsg
                ];
            }
        }

        // Ensure current active user is in the list even if no messages yet
        $found = false;
        foreach ($usersWithMessages as $um) {
            if ($um['user']->getId() === $otherUser->getId()) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            array_unshift($usersWithMessages, ['user' => $otherUser, 'lastMessage' => null]);
        }

        return $this->render('chat/index.html.twig', [
            'conversations' => $usersWithMessages,
            'messages' => $messages,
            'activeUser' => $otherUser
        ]);
    }

    #[Route('/chat/send/{id}', name: 'app_chat_send', methods: ['POST'])]
    public function send(User $recipient, Request $request, EntityManagerInterface $em): Response
    {
        $sender = $this->getUser();
        $content = $request->request->get('content');

        if ($content) {
            $message = new Message();
            $message->setSender($sender);
            $message->setRecipient($recipient);
            $message->setContent($content);

            $em->persist($message);
            $em->flush();
        }

        return $this->redirectToRoute('app_chat_conversation', ['id' => $recipient->getId()]);
    }
}
