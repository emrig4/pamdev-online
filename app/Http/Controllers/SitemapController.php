<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Modules\Resource\Models\Resource;

class SitemapController extends Controller
{
    /**
     * Generate and return XML sitemap
     */
    public function index()
    {
        if (ob_get_level()) {
            ob_clean();
        }

        // Static pages
        $pages = [
            ['url' => 'https://pamdev.online/', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '1.0'],
            ['url' => 'https://pamdev.online/about-us', 'lastmod' => now(), 'changefreq' => 'monthly', 'priority' => '0.8'],
            ['url' => 'https://pamdev.online/how-it-works', 'lastmod' => now(), 'changefreq' => 'monthly', 'priority' => '0.8'],
            ['url' => 'https://pamdev.online/faq', 'lastmod' => now(), 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['url' => 'https://pamdev.online/privacy-policy', 'lastmod' => now(), 'changefreq' => 'yearly', 'priority' => '0.5'],
            ['url' => 'https://pamdev.online/pricings', 'lastmod' => now(), 'changefreq' => 'monthly', 'priority' => '0.8'],
            ['url' => 'https://pamdev.online/resources/upload', 'lastmod' => now(), 'changefreq' => 'weekly', 'priority' => '0.9'],
            ['url' => 'https://pamdev.online/resources/fields', 'lastmod' => now(), 'changefreq' => 'weekly', 'priority' => '0.8'],
            ['url' => 'https://pamdev.online/blog', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8'],
        ];

        // Add dynamic project topics/resources
        Resource::where('is_published', 1)
            ->select(['slug', 'updated_at'])
            ->chunk(500, function ($resources) use (&$pages) {
                foreach ($resources as $resource) {
                    $pages[] = [
                        'url' => 'https://pamdev.online/resources/' . $resource->slug,
                        'lastmod' => $resource->updated_at,
                        'changefreq' => 'monthly',
                        'priority' => '0.7',
                    ];
                }
            });

        // Build XML
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($pages as $page) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($page['url']) . "</loc>\n";
            $xml .= "    <lastmod>" . date('Y-m-d\TH:i:s\Z', strtotime($page['lastmod'])) . "</lastmod>\n";
            $xml .= "    <changefreq>" . $page['changefreq'] . "</changefreq>\n";
            $xml .= "    <priority>" . $page['priority'] . "</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600',
            'Content-Length' => strlen($xml),
        ]);
    }
}