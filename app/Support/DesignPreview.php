<?php

namespace App\Support;

/**
 * Resolves full product+design mockup preview paths for customized shop items.
 * Never treats design JSON, print plates, or raw artwork-layer files as mockups.
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
     * True only for composited mockup files (preview-*.png), not artwork/print/json.
     */
    public static function isMockupPath(?string $path): bool
    {
        if (! self::isImagePath($path)) {
            return false;
        }

        $base = strtolower(basename($path));

        // Explicit mockup exports from the customizer
        if (str_starts_with($base, 'preview-')) {
            return true;
        }

        // Reject known non-mockup design assets
        if (str_starts_with($base, 'artwork-')
            || str_starts_with($base, 'print-')
            || str_starts_with($base, 'design-')
            || str_ends_with($base, '.json')) {
            return false;
        }

        return false;
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
            if (is_string($path)) {
                $candidates[] = $path;
            }
        }

        if (is_string($designFilePath) && $designFilePath !== '') {
            $candidates[] = $designFilePath;
        }

        foreach ($candidates as $candidate) {
            if (self::isMockupPath($candidate)) {
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
