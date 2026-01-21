<?php

namespace App\Service;

use Cloudinary\Cloudinary;

class CloudinaryService
{
    private Cloudinary $cloudinary;

    public function __construct(string $cloudinaryUrl)
    {
        // Parse cloudinary://API_KEY:API_SECRET@CLOUD_NAME
        $this->cloudinary = new Cloudinary($cloudinaryUrl);
    }

    public function uploadFile(string $filePath, string $folder = 'bricolage_uploads'): array
    {
        try {
            $uploadApi = $this->cloudinary->uploadApi();
            $result = $uploadApi->upload($filePath, [
                'folder' => $folder,
                'resource_type' => 'auto'
            ]);

            return [
                'success' => true,
                'url' => $result['secure_url'],
                'public_id' => $result['public_id'],
                'format' => $result['format'] ?? null,
                'resource_type' => $result['resource_type']
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
