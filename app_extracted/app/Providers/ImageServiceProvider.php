<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\Services\ImageOptimizer;

/**
 * Image Optimization Blade Directives Provider
 * Provides easy-to-use Blade directives for optimized images
 */
class ImageServiceProvider extends ServiceProvider
{
    protected $imageOptimizer;

    public function __construct($app)
    {
        parent::__construct($app);
        $this->imageOptimizer = new ImageOptimizer();
    }

    /**
     * Register services
     */
    public function register()
    {
        $this->app->singleton('image.optimizer', function ($app) {
            return new ImageOptimizer();
        });
    }

    /**
     * Bootstrap services
     */
    public function boot()
    {
        // @optimizedImage directive
        Blade::directive('optimizedImage', function ($expression) {
            return "<?php echo app('image.optimizer')->generateLazyImageHtml($expression)['html']; ?>";
        });

        // @responsiveImage directive
        Blade::directive('responsiveImage', function ($expression) {
            $args = explode(',', str_replace(['(', ')', ' ', "'", '"'], '', $expression));
            
            $path = $args[0] ?? '';
            $alt = $args[1] ?? '';
            $class = $args[2] ?? 'img-fluid';
            $sizes = $args[3] ?? '(max-width: 768px) 100vw, 50vw';
            
            return "<?php 
                \$imageData = app('image.optimizer')->generateLazyImageHtml($path, $alt, $class, $sizes);
                echo '<picture>' . 
                     (isset(\$imageData['webp_srcset']) ? '<source srcset=\"' . \$imageData['webp_srcset'] . '\" type=\"image/webp\">' : '') . 
                     '<source srcset=\"' . \$imageData['srcset'] . '\" type=\"image/jpeg\">' . 
                     '<img src=\"' . explode(' ', \$imageData['srcset'])[0] . '\" alt=\"' . htmlspecialchars($alt) . '\" class=\"' . $class . '\" loading=\"lazy\">' . 
                     '</picture>';
            ?>";
        });

        // @lazyImage directive
        Blade::directive('lazyImage', function ($expression) {
            return "<?php echo app('image.optimizer')->generateLazyImageHtml($expression)['html']; ?>";
        });

        // @imageUrl directive - gets optimized URL
        Blade::directive('imageUrl', function ($expression) {
            return "<?php echo app('image.optimizer')->getOptimizedUrl($expression); ?>";
        });

        // @imageSrcset directive - gets srcset for responsive images
        Blade::directive('imageSrcset', function ($expression) {
            return "<?php echo app('image.optimizer')->generateSrcset($expression); ?>";
        });

        // @webpImage directive - generates WebP with fallback
        Blade::directive('webpImage', function ($expression) {
            return "<?php 
                \$imagePath = $expression;
                \$webpUrl = app('image.optimizer')->getOptimizedUrl(\$imagePath, 'desktop', 'webp');
                \$fallbackUrl = app('image.optimizer')->getOptimizedUrl(\$imagePath, 'desktop', 'jpg');
                
                echo '<picture>' .
                     '<source srcset=\"' . \$webpUrl . '\" type=\"image/webp\">' .
                     '<img src=\"' . \$fallbackUrl . '\" loading=\"lazy\" alt=\"' . basename(\$imagePath) . '\">' .
                     '</picture>';
            ?>";
        });
    }
}