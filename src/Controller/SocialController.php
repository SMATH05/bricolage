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
    public function createPost(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
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
                $errorMessages = [
                    UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
                    UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
                    UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded',
                    UPLOAD_ERR_NO_FILE => 'No file was uploaded',
                    UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder',
                    UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
                    UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload',
                ];
                
                $errorMsg = $errorMessages[$mediaFile->getError()] ?? 'Unknown upload error';
                error_log('Upload error code: ' . $mediaFile->getError() . ' - ' . $errorMsg);
                $this->addFlash('error', 'Upload error: ' . $errorMsg);
                return $this->redirectToRoute('app_social_feed');
            }
            
            $originalFilename = pathinfo($mediaFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $mediaFile->guessExtension();
            
            // Debug: Log upload info
            error_log('Upload debug - Original: ' . $originalFilename . ', New: ' . $newFilename);
            error_log('Upload debug - Directory: ' . $this->getParameter('posts_directory'));
            error_log('Upload debug - File size: ' . $mediaFile->getSize());
            error_log('Upload debug - Mime type: ' . $mediaFile->getMimeType());
            error_log('Upload debug - Temp file: ' . $mediaFile->getRealPath());

            try {
                // Ensure directory exists and is writable
                $uploadDir = $this->getParameter('posts_directory');
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                    error_log('Created upload directory: ' . $uploadDir);
                }
                
                if (!is_writable($uploadDir)) {
                    error_log('Upload directory is not writable: ' . $uploadDir);
                    $this->addFlash('error', 'Upload directory is not writable');
                    return $this->redirectToRoute('app_social_feed');
                }
                
                $mediaFile->move(
                    $uploadDir,
                    $newFilename
                );
                $post->setMedia($newFilename);
                
                error_log('Upload debug - File moved successfully: ' . $newFilename);
                error_log('Upload debug - Full path: ' . $uploadDir . '/' . $newFilename);

                $ext = strtolower($mediaFile->guessExtension());
                if (in_array($ext, ['mp4', 'webm', 'ogg', 'mov'])) {
                    $post->setMediaType('video');
                } else {
                    $post->setMediaType('image');
                }
            } catch (\Exception $e) {
                error_log('Upload exception: ' . $e->getMessage());
                error_log('Upload exception trace: ' . $e->getTraceAsString());
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
