<?php

namespace App\Service;

use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

class CloudinaryService
{
    private Cloudinary $cloudinary;

    public function __construct(string $cloudinaryUrl)
    {
        Configuration::instance($cloudinaryUrl);
        $this->cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => parse_url($cloudinaryUrl, PHP_URL_HOST),
                'api_key' => parse_url($cloudinaryUrl, PHP_URL_USER),
                'api_secret' => parse_url($cloudinaryUrl, PHP_URL_PASS),
            ],
            'url' => [
                'secure' => true
            ]
        ]);
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
