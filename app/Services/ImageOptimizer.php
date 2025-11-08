<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;
use Intervention\Image\Filters\FilterInterface;
use Illuminate\Support\Str;

/**
 * Advanced Image Optimization Service for Authoran Laravel Project
 * Uses Intervention Image (already installed) for server-side optimization
 */
class ImageOptimizer
{
    /**
     * Configuration
     */
    protected $config = [
        'quality' => 85,
        'progressive' => true,
        'webp_quality' => 80,
        'sizes' => [
            'thumbnail' => ['width' => 150, 'height' => 150],
            'small' => ['width' => 300, 'height' => 200],
            'medium' => ['width' => 600, 'height' => 400],
            'large' => ['width' => 1000, 'height' => 700],
            'xl' => ['width' => 1200, 'height' => 800],
        ],
        'formats' => ['webp', 'jpg', 'png'],
        'storage_disk' => 'public',
    ];

    /**
     * Optimize and store image with multiple sizes and formats
     *
     * @param string $sourcePath Source image path or uploaded file
     * @param string $destinationFolder Destination folder in storage
     * @param array $options Additional options
     * @return array Optimized image information
     */
    public function optimizeAndStore($sourcePath, $destinationFolder = 'images', $options = [])
    {
        // Merge options with config
        $this->config = array_merge($this->config, $options);
        
        // Generate unique filename
        $filename = $this->generateFilename($sourcePath);
        $basePath = $destinationFolder . '/' . $filename;
        
        // Get original image info
        $originalInfo = $this->getImageInfo($sourcePath);
        
        $results = [
            'original' => $originalInfo,
            'variants' => [],
            'webp_variants' => [],
            'filename' => $filename,
            'base_path' => $basePath,
        ];

        // Generate all sizes
        foreach ($this->config['sizes'] as $sizeName => $dimensions) {
            // JPEG/PNG variants
            $jpgPath = $this->generateImageVariant($sourcePath, $basePath, $sizeName, $dimensions, 'jpg');
            $results['variants'][$sizeName] = $jpgPath;
            
            // WebP variants (if supported)
            if (function_exists('imagewebp')) {
                $webpPath = $this->generateImageVariant($sourcePath, $basePath, $sizeName, $dimensions, 'webp');
                $results['webp_variants'][$sizeName] = $webpPath;
            }
        }

        // Generate responsive srcset
        $results['srcset'] = $this->generateSrcset($results['variants']);
        $results['webp_srcset'] = $this->generateSrcset($results['webp_variants']);

        return $results;
    }

    /**
     * Generate image variant with specific size and format
     */
    protected function generateImageVariant($sourcePath, $basePath, $sizeName, $dimensions, $format)
    {
        // Open and process image
        $image = Image::make($sourcePath);
        
        // Resize with smart cropping
        $image->fit($dimensions['width'], $dimensions['height'], function ($constraint) {
            $constraint->upsize();
        });

        // Apply optimizations
        if ($format === 'jpg') {
            $image->save($this->getStoragePath($this->getVariantPath($basePath, $sizeName, 'jpg')), $this->config['quality']);
        } elseif ($format === 'webp') {
            $image->save($this->getStoragePath($this->getVariantPath($basePath, $sizeName, 'webp')), $this->config['webp_quality']);
        } elseif ($format === 'png') {
            $image->save($this->getStoragePath($this->getVariantPath($basePath, $sizeName, 'png')), 90);
        }

        $image->destroy();
        
        return $this->getVariantPath($basePath, $sizeName, $format);
    }

    /**
     * Generate responsive srcset string
     */
    protected function generateSrcset($variants)
    {
        $srcset = [];
        foreach ($variants as $sizeName => $path) {
            $width = $this->config['sizes'][$sizeName]['width'];
            $url = Storage::disk($this->config['storage_disk'])->url($path);
            $srcset[] = "{$url} {$width}w";
        }
        return implode(', ', $srcset);
    }

    /**
     * Get optimized image URL based on device size
     */
    public function getOptimizedUrl($imagePath, $device = 'desktop', $format = 'auto')
    {
        $sizes = [
            'mobile' => 'small',
            'tablet' => 'medium',
            'desktop' => 'large',
            'xl' => 'xl'
        ];

        $sizeKey = $sizes[$device] ?? 'medium';
        
        // Check for WebP support
        if ($format === 'auto') {
            $format = $this->supportsWebP() ? 'webp' : 'jpg';
        }

        $variantPath = $this->getVariantPath($imagePath, $sizeKey, $format);
        
        if (Storage::disk($this->config['storage_disk'])->exists($variantPath)) {
            return Storage::disk($this->config['storage_disk'])->url($variantPath);
        }

        // Fallback to original
        return Storage::disk($this->config['storage_disk'])->url($imagePath);
    }

    /**
     * Generate lazy loading image HTML
     */
    public function generateLazyImageHtml($imagePath, $alt = '', $class = '', $sizes = '100vw')
    {
        $baseName = pathinfo($imagePath, PATHINFO_FILENAME);
        $directory = pathinfo($imagePath, PATHINFO_DIRNAME);
        
        $results = [
            'html' => '',
            'srcset' => [],
            'sizes' => $sizes
        ];

        // Generate srcset for all variants
        $srcsetItems = [];
        foreach ($this->config['sizes'] as $sizeName => $dimensions) {
            $variantPath = $directory . '/' . $baseName . '_' . $sizeName . '.jpg';
            if (Storage::disk($this->config['storage_disk'])->exists($variantPath)) {
                $url = Storage::disk($this->config['storage_disk'])->url($variantPath);
                $srcsetItems[] = "{$url} {$dimensions['width']}w";
            }
        }

        $srcset = implode(', ', $srcsetItems);
        
        // Use smallest image as fallback
        $fallbackPath = $directory . '/' . $baseName . '_small.jpg';
        if (Storage::disk($this->config['storage_disk'])->exists($fallbackPath)) {
            $fallbackUrl = Storage::disk($this->config['storage_disk'])->url($fallbackPath);
        } else {
            $fallbackUrl = Storage::disk($this->config['storage_disk'])->url($imagePath);
        }

        $results['html'] = sprintf(
            '<img src="%s" srcset="%s" sizes="%s" alt="%s" class="%s" loading="lazy">',
            $fallbackUrl,
            $srcset,
            $sizes,
            htmlspecialchars($alt),
            htmlspecialchars($class)
        );

        return $results;
    }

    /**
     * Check if WebP is supported
     */
    protected function supportsWebP()
    {
        return function_exists('imagewebp') && 
               extension_loaded('gd');
    }

    /**
     * Get image information
     */
    protected function getImageInfo($path)
    {
        $image = Image::make($path);
        $info = [
            'width' => $image->width(),
            'height' => $image->height(),
            'size' => $image->filesize(),
            'mime' => $image->mime(),
        ];
        $image->destroy();
        return $info;
    }

    /**
     * Generate unique filename
     */
    protected function generateFilename($sourcePath)
    {
        $extension = pathinfo($sourcePath, PATHINFO_EXTENSION);
        return Str::uuid() . '.' . $extension;
    }

    /**
     * Get variant path
     */
    protected function getVariantPath($basePath, $size, $format)
    {
        $pathInfo = pathinfo($basePath);
        return $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '_' . $size . '.' . $format;
    }

    /**
     * Get full storage path
     */
    protected function getStoragePath($path)
    {
        return Storage::disk($this->config['storage_disk'])->path($path);
    }

    /**
     * Optimize existing images in batch
     */
    public function batchOptimize($directory, $recursive = true)
    {
        $files = Storage::disk($this->config['storage_disk'])->allFiles($directory);
        $results = [];

        foreach ($files as $file) {
            if (in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png'])) {
                $results[] = $this->optimizeAndStore($this->getStoragePath($file), pathinfo($file, PATHINFO_DIRNAME));
            }
        }

        return $results;
    }
}