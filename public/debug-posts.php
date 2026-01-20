<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Kernel;
use Symfony\Component\Dotenv\Dotenv;

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$kernel->boot();

$entityManager = $kernel->getContainer()->get('doctrine.orm.entity_manager');

echo '<h2>Recent Posts Debug</h2>';

try {
    $posts = $entityManager->createQuery('SELECT p FROM App\Entity\Post p ORDER BY p.createdAt DESC')
        ->setMaxResults(5)
        ->getResult();
    
    foreach ($posts as $post) {
        echo '<div style="border: 1px solid #ccc; padding: 10px; margin: 10px 0;">';
        echo '<h4>Post ID: ' . $post->getId() . '</h4>';
        echo '<p><strong>Author:</strong> ' . $post->getAuthor()->getUserIdentifier() . '</p>';
        echo '<p><strong>Content:</strong> ' . ($post->getContent() ?: 'No content') . '</p>';
        echo '<p><strong>Media:</strong> ' . ($post->getMedia() ?: 'No media') . '</p>';
        echo '<p><strong>Media Type:</strong> ' . $post->getMediaType() . '</p>';
        echo '<p><strong>Created:</strong> ' . $post->getCreatedAt()->format('Y-m-d H:i:s') . '</p>';
        
        if ($post->getMedia()) {
            $mediaPath = __DIR__ . '/uploads/posts/' . $post->getMedia();
            echo '<p><strong>File exists:</strong> ' . (file_exists($mediaPath) ? 'Yes' : 'No') . '</p>';
            echo '<p><strong>File path:</strong> ' . $mediaPath . '</p>';
            if (file_exists($mediaPath)) {
                echo '<p><strong>File size:</strong> ' . filesize($mediaPath) . ' bytes</p>';
            }
        }
        
        echo '</div>';
    }
} catch (Exception $e) {
    echo '<p style="color: red;">Error: ' . $e->getMessage() . '</p>';
}
?>
