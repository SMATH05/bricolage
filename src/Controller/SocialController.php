<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\PostComment;
use App\Entity\PostLike;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class SocialController extends AbstractController
{
    #[Route('/feed', name: 'app_social_feed')]
    public function feed(PostRepository $postRepository): Response
    {
        try {
            $posts = $postRepository->findAllByDate();
        } catch (\Exception $e) {
            $posts = [];
        }

        return $this->render('social/feed.html.twig', [
            'posts' => $posts
        ]);
    }

    #[Route('/social/post', name: 'app_social_post_create', methods: ['POST'])]
    public function createPost(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $content = $request->request->get('content');
        $mediaFile = $request->files->get('media');

        if (!$content && !$mediaFile) {
            $this->addFlash('error', 'Le post ne peut pas être vide.');
            return $this->redirectToRoute('app_social_feed');
        }

        $post = new Post();
        $post->setContent($content);
        $post->setAuthor($this->getUser());

        if ($mediaFile) {
            $originalFilename = pathinfo($mediaFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $mediaFile->guessExtension();

            try {
                $mediaFile->move(
                    $this->getParameter('posts_directory'),
                    $newFilename
                );
                $post->setMedia($newFilename);

                $ext = strtolower($mediaFile->guessExtension());
                if (in_array($ext, ['mp4', 'webm', 'ogg', 'mov'])) {
                    $post->setMediaType('video');
                } else {
                    $post->setMediaType('image');
                }
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de l\'upload du média.');
            }
        }

        $entityManager->persist($post);
        $entityManager->flush();

        return $this->redirectToRoute('app_social_feed');
    }

    #[Route('/social/like/{id}', name: 'app_social_like', methods: ['POST'])]
    public function like(Post $post, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 403);
        }

        $likeRepo = $entityManager->getRepository(PostLike::class);
        $like = $likeRepo->findOneBy(['post' => $post, 'user' => $user]);

        if ($like) {
            $entityManager->remove($like);
            $liked = false;
        } else {
            $like = new PostLike();
            $like->setPost($post);
            $like->setUser($user);
            $entityManager->persist($like);
            $liked = true;
        }

        $entityManager->flush();

        return new JsonResponse([
            'liked' => $liked,
            'likesCount' => $post->getLikes()->count()
        ]);
    }

    #[Route('/social/comment/{id}', name: 'app_social_comment', methods: ['POST'])]
    public function comment(Post $post, Request $request, EntityManagerInterface $entityManager): Response
    {
        $content = $request->request->get('content');
        if (!$content) {
            return $this->redirectToRoute('app_social_feed');
        }

        $comment = new PostComment();
        $comment->setContent($content);
        $comment->setAuthor($this->getUser());
        $comment->setPost($post);

        $entityManager->persist($comment);
        $entityManager->flush();

        return $this->redirectToRoute('app_social_feed');
    }
}
