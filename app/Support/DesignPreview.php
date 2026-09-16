<?php

namespace App\Support;

/**
 * Resolves usable image preview paths for customized shop cart/order items.
 * Never treats design JSON metadata files as preview images.
 */
class DesignPreview
{
    private const IMAGE_EXTENSIONS = ['png', 'jpg', 'jpeg', 'gif', 'webp', 'svg'];

    public static function isImagePath(?string $path): bool
    {
        if (! is_string($path) || $path === '') {
            return false;
        }

        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return in_array($ext, self::IMAGE_EXTENSIONS, true);
    }

    /**
     * @param  array<int, mixed>|null  $assetPaths
     * @param  array<string, mixed>|null  $designData
     */
    public static function resolvePath(
        ?string $designFilePath,
        ?array $assetPaths = null,
        ?array $designData = null,
    ): ?string {
        $candidates = [];

        foreach ($assetPaths ?? [] as $path) {
            if (is_string($path) && str_contains(basename($path), 'preview-')) {
                $candidates[] = $path;
            }
        }

        if (is_string($designFilePath) && $designFilePath !== '') {
            $candidates[] = $designFilePath;
        }

        if (! empty($designData['print_file_path']) && is_string($designData['print_file_path'])) {
            $candidates[] = $designData['print_file_path'];
        }

        if (! empty($designData['artwork']['asset_path']) && is_string($designData['artwork']['asset_path'])) {
            $candidates[] = $designData['artwork']['asset_path'];
        }

        foreach ($assetPaths ?? [] as $path) {
            if (is_string($path) && self::isImagePath($path)) {
                $candidates[] = $path;
            }
        }

        foreach ($candidates as $candidate) {
            if (self::isImagePath($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    /**
     * @param  array<int, mixed>|null  $assetPaths
     * @param  array<string, mixed>|null  $designData
     */
    public static function resolveUrl(
        ?string $designFilePath,
        ?array $assetPaths = null,
        ?array $designData = null,
    ): ?string {
        $path = self::resolvePath($designFilePath, $assetPaths, $designData);

        return $path ? asset('storage/'.$path) : null;
    }
}
