<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ImageOptimizer;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Image Upload Controller with Automatic Optimization
 * Integrates with the ImageOptimizer service for best performance
 */
class ImageUploadController extends Controller
{
    protected $imageOptimizer;

    public function __construct(ImageOptimizer $imageOptimizer)
    {
        $this->imageOptimizer = $imageOptimizer;
    }

    /**
     * Handle resource cover image upload with optimization
     */
    public function uploadResourceCover(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240', // 10MB max
        ]);

        try {
            // Store original temporarily
            $uploadedFile = $request->file('image');
            $tempPath = $uploadedFile->store('temp', 'public');
            
            // Optimize and store
            $optimizedResult = $this->imageOptimizer->optimizeAndStore(
                Storage::disk('public')->path($tempPath),
                'resource-covers',
                [
                    'quality' => 85,
                    'webp_quality' => 80,
                ]
            );

            // Clean up temporary file
            Storage::disk('public')->delete($tempPath);

            return response()->json([
                'success' => true,
                'message' => 'Image uploaded and optimized successfully',
                'data' => [
                    'original_url' => Storage::disk('public')->url($optimizedResult['base_path'] . '.jpg'),
                    'webp_url' => isset($optimizedResult['webp_variants']['medium']) ? 
                                  Storage::disk('public')->url($optimizedResult['webp_variants']['medium']) : null,
                    'srcset' => $optimizedResult['srcset'],
                    'webp_srcset' => $optimizedResult['webp_srcset'],
                    'filename' => $optimizedResult['filename'],
                    'sizes' => array_keys($optimizedResult['variants'])
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Image upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle blog image upload with optimization
     */
    public function uploadBlogImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
        ]);

        try {
            $uploadedFile = $request->file('image');
            $tempPath = $uploadedFile->store('temp', 'public');
            
            // Optimize specifically for blog content
            $optimizedResult = $this->imageOptimizer->optimizeAndStore(
                Storage::disk('public')->path($tempPath),
                'blog_images',
                [
                    'quality' => 80,
                    'webp_quality' => 75,
                ]
            );

            Storage::disk('public')->delete($tempPath);

            return response()->json([
                'success' => true,
                'message' => 'Blog image uploaded and optimized',
                'data' => [
                    'original_url' => Storage::disk('public')->url($optimizedResult['base_path'] . '.jpg'),
                    'medium_url' => Storage::disk('public')->url($optimizedResult['variants']['medium']),
                    'large_url' => Storage::disk('public')->url($optimizedResult['variants']['large']),
                    'webp_medium' => isset($optimizedResult['webp_variants']['medium']) ? 
                                     Storage::disk('public')->url($optimizedResult['webp_variants']['medium']) : null,
                    'srcset' => $optimizedResult['srcset'],
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Blog image upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle user avatar upload with optimization
     */
    public function uploadUserAvatar(Request $request, $userId = null)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048', // 2MB max
        ]);

        try {
            $uploadedFile = $request->file('avatar');
            $tempPath = $uploadedFile->store('temp', 'public');
            
            // User avatars need high quality but small sizes
            $optimizedResult = $this->imageOptimizer->optimizeAndStore(
                Storage::disk('public')->path($tempPath),
                'avatars',
                [
                    'quality' => 90,
                    'webp_quality' => 85,
                    'sizes' => [
                        'thumbnail' => ['width' => 100, 'height' => 100],
                        'small' => ['width' => 200, 'height' => 200],
                        'medium' => ['width' => 400, 'height' => 400],
                    ]
                ]
            );

            Storage::disk('public')->delete($tempPath);

            return response()->json([
                'success' => true,
                'message' => 'Avatar uploaded and optimized',
                'data' => [
                    'avatar_url' => Storage::disk('public')->url($optimizedResult['variants']['medium']),
                    'thumbnail_url' => Storage::disk('public')->url($optimizedResult['variants']['thumbnail']),
                    'webp_avatar' => isset($optimizedResult['webp_variants']['medium']) ? 
                                     Storage::disk('public')->url($optimizedResult['webp_variants']['medium']) : null,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Avatar upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get optimized image for display
     */
    public function getOptimizedImage(Request $request, $path)
    {
        $device = $request->get('device', 'desktop');
        $format = $request->get('format', 'auto');
        
        $optimizedUrl = $this->imageOptimizer->getOptimizedUrl($path, $device, $format);
        
        if ($optimizedUrl) {
            return redirect($optimizedUrl);
        }
        
        abort(404);
    }

    /**
     * API endpoint for JavaScript image optimization
     */
    public function apiOptimize(Request $request)
    {
        $request->validate([
            'image_path' => 'required|string',
            'device' => 'string|in:mobile,tablet,desktop,xl',
            'format' => 'string|in:auto,webp,jpg,png',
        ]);

        try {
            $imagePath = $request->image_path;
            $device = $request->get('device', 'desktop');
            $format = $request->get('format', 'auto');
            
            $lazyImageData = $this->imageOptimizer->generateLazyImageHtml(
                $imagePath,
                $request->get('alt', ''),
                $request->get('class', ''),
                $request->get('sizes', '100vw')
            );
            
            return response()->json([
                'success' => true,
                'data' => $lazyImageData
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Image optimization failed: ' . $e->getMessage()
            ], 500);
        }
    }
}