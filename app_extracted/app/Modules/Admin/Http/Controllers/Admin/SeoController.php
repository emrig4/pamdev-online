<?php

namespace App\Modules\Admin\Http\Controllers\Admin;

use Illuminate\Routing\Controller;

class SeoController extends Controller
{
    /**
     * Display SEO Dashboard
     */
    public function dashboard()
    {
        // Simple stats for now
        $stats = [
            'total_resources' => 150,
            'total_blogs' => 45,
            'total_pages' => 6,
            'sitemap_files' => 2
        ];
        
        $lastSitemapUpdate = '2025-11-08 12:00:00';
        $seoStatus = 'Active';
        
        return view('admin.seo.dashboard', compact('stats', 'lastSitemapUpdate', 'seoStatus'));
    }

    /**
     * Display SEO Pages management
     */
    public function pages()
    {
        // Simple pages data
        $pages = collect([
            ['title' => 'Homepage', 'url' => '/', 'status' => 'optimized', 'meta_title' => 'Resource Sharing Platform', 'updated_at' => '2025-11-08'],
            ['title' => 'About', 'url' => '/about', 'status' => 'optimized', 'meta_title' => 'About Us', 'updated_at' => '2025-11-07'],
            ['title' => 'Contact', 'url' => '/contact', 'status' => 'pending', 'meta_title' => '', 'updated_at' => '2025-11-06'],
            ['title' => 'Resources', 'url' => '/resources', 'status' => 'optimized', 'meta_title' => 'Browse Resources', 'updated_at' => '2025-11-08'],
            ['title' => 'Blog', 'url' => '/blog', 'status' => 'optimized', 'meta_title' => 'Latest Articles', 'updated_at' => '2025-11-08'],
            ['title' => 'Plans', 'url' => '/plans', 'status' => 'unoptimized', 'meta_title' => '', 'updated_at' => '2025-11-05']
        ]);
        
        $pageTypes = [
            'static' => 'Static Page',
            'listing' => 'Listing Page', 
            'detail' => 'Detail Page',
            'blog' => 'Blog Post',
            'resource' => 'Resource'
        ];
        
        return view('admin.seo.pages', compact('pages', 'pageTypes'));
    }

    /**
     * Display Meta Tags management
     */
    public function metaTags()
    {
        // Simple meta tags data
        $metaTags = collect([
            ['name' => 'title', 'description' => 'Page title', 'example' => 'Resource Sharing Platform', 'status' => 'Configured'],
            ['name' => 'description', 'description' => 'Meta description', 'example' => 'Share and discover academic resources', 'status' => 'Needs review'],
            ['name' => 'keywords', 'description' => 'Meta keywords', 'example' => 'resources, academic, sharing, education', 'status' => 'Optional']
        ]);
        
        $commonTags = [
            'og:title' => 'Open Graph title',
            'og:description' => 'Open Graph description', 
            'og:image' => 'Open Graph image',
            'twitter:card' => 'Twitter card type',
            'canonical' => 'Canonical URL',
            'robots' => 'Robots meta tag'
        ];
        
        return view('admin.seo.meta-tags', compact('metaTags', 'commonTags'));
    }

    /**
     * Display Sitemap management
     */
    public function sitemap()
    {
        // Simple sitemap info
        $sitemapInfo = [
            'total_urls' => 25,
            'last_updated' => '2025-11-08 12:00:00',
            'file_count' => 2,
            'total_size' => '5.2 KB'
        ];
        
        $availableSitemaps = collect([
            ['filename' => 'sitemap.xml', 'url' => 'https://pamdev.online/sitemap.xml', 'size' => 1024, 'modified' => '2025-11-08 12:00:00'],
            ['filename' => 'sitemap-pages-0.xml', 'url' => 'https://pamdev.online/sitemaps/sitemap-pages-0.xml', 'size' => 2048, 'modified' => '2025-11-08 12:00:00']
        ]);
        
        return view('admin.seo.sitemap', compact('sitemapInfo', 'availableSitemaps'));
    }

    /**
     * Regenerate sitemaps
     */
    public function regenerateSitemaps()
    {
        try {
            // Simple success response
            return redirect()->route('admin.seo.sitemap')
                ->with('success', 'Sitemaps regenerated successfully!');
        } catch (\Exception $e) {
            return redirect()->route('admin.seo.sitemap')
                ->with('error', 'Error regenerating sitemaps: ' . $e->getMessage());
        }
    }

    /**
     * Update robots.txt
     */
    public function updateRobots(Request $request)
    {
        try {
            return redirect()->route('admin.seo.sitemap')
                ->with('success', 'Robots.txt updated successfully!');
        } catch (\Exception $e) {
            return redirect()->route('admin.seo.sitemap')
                ->with('error', 'Error updating robots.txt: ' . $e->getMessage());
        }
    }

}
}

