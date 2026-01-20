<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TestUploadController extends AbstractController
{
    #[Route('/test-upload', name: 'app_test_upload')]
    public function testUpload(): Response
    {
        $postsDir = $this->getParameter('posts_directory');
        $debug = [
            'posts_directory' => $postsDir,
            'directory_exists' => is_dir($postsDir),
            'directory_writable' => is_writable($postsDir),
            'files_in_directory' => scandir($postsDir),
            'base_url' => $this->getParameter('base_url'),
        ];
        
        return $this->render('test/upload.html.twig', [
            'debug' => $debug
        ]);
    }
}
