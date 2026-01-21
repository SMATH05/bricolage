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
    #[Route('/accueil', name: 'app_social_feed')]
    public function feed(PostRepository $postRepository): Response
    {
        try {
            $posts = $postRepository->findAllByDate();
        } catch (\Exception $e) {
            $this->addFlash('error', 'Feed Error: ' . $e->getMessage());
            $posts = [];
        }

        return $this->render('social/feed.html.twig', [
            'posts' => $posts
        ]);
    }

    #[Route('/social/post', name: 'app_social_post_create', methods: ['POST'])]
    public function createPost(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger, \App\Service\CloudinaryService $cloudinaryService): Response
    {
        $content = $request->request->get('content');
        $mediaFile = $request->files->get('media');

        // Debug: Log request data
        error_log('Request debug - Content: ' . $content);
        error_log('Request debug - Has file: ' . ($mediaFile ? 'Yes' : 'No'));
        if ($mediaFile) {
            error_log('Request debug - File name: ' . $mediaFile->getClientOriginalName());
            error_log('Request debug - File size: ' . $mediaFile->getSize());
            error_log('Request debug - File error: ' . $mediaFile->getError());
        }

        if (!$content && !$mediaFile) {
            $this->addFlash('error', 'Le post ne peut pas être vide.');
            return $this->redirectToRoute('app_social_feed');
        }

        $post = new Post();
        $post->setContent($content);
        $post->setAuthor($this->getUser());

        if ($mediaFile) {
            // Check for upload errors
            if ($mediaFile->getError() !== UPLOAD_ERR_OK) {
                // ... keeps validation which is good ...
                $this->addFlash('error', 'Upload error code: ' . $mediaFile->getError());
                return $this->redirectToRoute('app_social_feed');
            }

            error_log('Cloudinary Upload started');

            try {
                // Upload to Cloudinary
                $result = $cloudinaryService->uploadFile($mediaFile->getRealPath(), 'bricolage_posts');

                if (!$result['success']) {
                    throw new \Exception('Cloudinary upload failed: ' . ($result['error'] ?? 'Unknown error'));
                }

                $fileUrl = $result['url'];
                $resourceType = $result['resource_type']; // image or video

                error_log('✅ File uploaded successfully to Cloudinary: ' . $fileUrl);

                $post->setMedia($fileUrl);
                $post->setMediaType($resourceType); // result is 'image' or 'video'

            } catch (\Exception $e) {
                error_log('Upload exception: ' . $e->getMessage());
                // error_log('Upload exception trace: ' . $e->getTraceAsString());
                $this->addFlash('error', 'Erreur lors de l\'upload du média: ' . $e->getMessage());
            }
        }

        try {
            $entityManager->persist($post);
            $entityManager->flush();

            // Debug: Log post creation
            error_log('Post created successfully - ID: ' . $post->getId() . ', Media: ' . ($post->getMedia() ?: 'None') . ', Type: ' . $post->getMediaType());

            $this->addFlash('success', 'Post created successfully! Media: ' . ($post->getMedia() ?: 'None'));
        } catch (\Exception $e) {
            error_log('Database error: ' . $e->getMessage());
            $this->addFlash('error', 'Erreur de publication : ' . $e->getMessage());
            return $this->redirectToRoute('app_social_feed');
        }

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
