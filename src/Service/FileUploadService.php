<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploadService
{
    private string $postsDirectory;
    private string $projectDir;

    public function __construct(string $projectDir)
    {
        $this->projectDir = $projectDir;
        $this->postsDirectory = $projectDir . '/public/uploads/posts';
    }

    public function uploadPostMedia(UploadedFile $file, string $newFilename): bool
    {
        try {
            // Create directory if it doesn't exist
            if (!is_dir($this->postsDirectory)) {
                if (!@mkdir($this->postsDirectory, 0777, true)) {
                    error_log('Failed to create directory: ' . $this->postsDirectory);
                    return false;
                }
            }

            // Ensure directory is writable
            if (!is_writable($this->postsDirectory)) {
                if (!@chmod($this->postsDirectory, 0777)) {
                    error_log('Directory exists but not writable: ' . $this->postsDirectory);
                    return false;
                }
            }

            $targetPath = $this->postsDirectory . '/' . $newFilename;
            
            // Use move instead of file_put_contents for better reliability
            if ($file->move($this->postsDirectory, $newFilename)) {
                error_log('File uploaded successfully: ' . $targetPath);
                error_log('File exists: ' . (file_exists($targetPath) ? 'Yes' : 'No'));
                error_log('File size: ' . (file_exists($targetPath) ? filesize($targetPath) : 'N/A'));
                return true;
            } else {
                error_log('Failed to move file to: ' . $targetPath);
                return false;
            }
        } catch (\Exception $e) {
            error_log('Upload error: ' . $e->getMessage());
            return false;
        }
    }

    public function getPostsDirectory(): string
    {
        return $this->postsDirectory;
    }
}
