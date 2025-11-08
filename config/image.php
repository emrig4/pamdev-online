<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Image Optimization Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the Authoran Laravel image optimization system
    |
    */

    // Default optimization settings
    'quality' => env('IMAGE_QUALITY', 85),
    'webp_quality' => env('IMAGE_WEBP_QUALITY', 80),
    'progressive' => env('IMAGE_PROGRESSIVE', true),

    // Image size definitions
    'sizes' => [
        'thumbnail' => [
            'width' => 150,
            'height' => 150,
            'description' => 'Small thumbnail for lists'
        ],
        'small' => [
            'width' => 300,
            'height' => 200,
            'description' => 'Small display images'
        ],
        'medium' => [
            'width' => 600,
            'height' => 400,
            'description' => 'Standard display size'
        ],
        'large' => [
            'width' => 1000,
            'height' => 700,
            'description' => 'Large display images'
        ],
        'xl' => [
            'width' => 1200,
            'height' => 800,
            'description' => 'Extra large images'
        ],
    ],

    // Responsive breakpoints
    'breakpoints' => [
        'mobile' => 768,
        'tablet' => 1024,
        'desktop' => 1200,
        'xl' => 1400,
    ],

    // Device-specific image sizes
    'device_sizes' => [
        'mobile' => 'small',
        'tablet' => 'medium', 
        'desktop' => 'large',
        'xl' => 'xl',
    ],

    // Supported image formats
    'formats' => ['webp', 'jpg', 'png'],
    'enable_webp' => env('IMAGE_ENABLE_WEBP', true),
    'webp_fallback' => env('IMAGE_WEBP_FALLBACK', 'jpg'),

    // Storage configuration
    'storage_disk' => env('IMAGE_STORAGE_DISK', 'public'),
    'temp_directory' => 'temp',
    'organize_by_type' => true,

    // Upload limits
    'max_file_size' => env('IMAGE_MAX_SIZE', 10485760), // 10MB
    'allowed_types' => ['jpeg', 'jpg', 'png', 'gif', 'webp'],

    // Lazy loading settings
    'lazy_loading' => [
        'enabled' => true,
        'placeholder_color' => '#f0f0f0',
        'blur_amount' => 10,
        'root_margin' => '50px 0px',
        'threshold' => 0.1,
    ],

    // Performance settings
    'cache_optimized' => env('IMAGE_CACHE_OPTIMIZED', true),
    'concurrent_processing' => env('IMAGE_CONCURRENT', true),
    'memory_limit' => '256M',

    // Directory organization
    'directories' => [
        'resource_covers' => 'resource-covers',
        'blog_images' => 'blog_images', 
        'user_avatars' => 'avatars',
        'theme_images' => 'themes/airdgereaders/images',
        'general' => 'images',
    ],

    // Optimization presets
    'presets' => [
        'default' => [
            'quality' => 85,
            'webp_quality' => 80,
            'sizes' => ['small', 'medium', 'large'],
        ],
        'high_quality' => [
            'quality' => 90,
            'webp_quality' => 85,
            'sizes' => ['small', 'medium', 'large', 'xl'],
        ],
        'fast_loading' => [
            'quality' => 75,
            'webp_quality' => 70,
            'sizes' => ['thumbnail', 'small', 'medium'],
        ],
        'blog' => [
            'quality' => 80,
            'webp_quality' => 75,
            'sizes' => ['medium', 'large'],
        ],
        'avatar' => [
            'quality' => 90,
            'webp_quality' => 85,
            'sizes' => ['thumbnail', 'small', 'medium'],
        ],
    ],

    // Auto-optimization rules
    'auto_optimize' => [
        'on_upload' => true,
        'batch_existing' => false,
        'formats' => ['jpg', 'jpeg', 'png'],
        'skip_gif' => true,
    ],

    // Error handling
    'fallback' => [
        'image' => 'themes/Airdgereaders/public/images/default-project-cover.png',
        'avatar' => 'themes/Airdgereaders/public/images/default-user-image.png',
        'blog' => 'themes/Airdgereaders/public/images/default-blog-cover.jpg',
    ],
];