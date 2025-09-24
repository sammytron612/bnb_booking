<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venue;
use App\Models\Review;
use Carbon\Carbon;

class SitemapController extends Controller
{
    /**
     * Generate sitemap index file
     */
    public function index()
    {
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $sitemap .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        // Main sitemap
        $sitemap .= $this->addSitemapUrl(route('sitemap.main'), Carbon::now());

        // Venues sitemap
        $sitemap .= $this->addSitemapUrl(route('sitemap.venues'), Carbon::now());

        $sitemap .= '</sitemapindex>';

        return response($sitemap)
            ->header('Content-Type', 'application/xml')
            ->header('Cache-Control', 'public, max-age=3600'); // Cache for 1 hour
    }

    /**
     * Generate main pages sitemap
     */
    public function main()
    {
        $urls = [
            [
                'url' => route('home'),
                'priority' => '1.0',
                'changefreq' => 'daily',
                'lastmod' => Carbon::now()->toISOString()
            ],
            [
                'url' => route('contact'),
                'priority' => '0.8',
                'changefreq' => 'monthly',
                'lastmod' => Carbon::now()->subDays(30)->toISOString()
            ],
            // Add more static pages as needed
        ];

        return $this->generateSitemap($urls);
    }

    /**
     * Generate venues sitemap
     */
    public function venues()
    {
        $venues = Venue::where('booking_enabled', true)
            ->orderBy('updated_at', 'desc')
            ->get();

        $urls = [];
        foreach ($venues as $venue) {
            $urls[] = [
                'url' => route('venue.show', $venue->route),
                'priority' => '0.9',
                'changefreq' => 'weekly',
                'lastmod' => $venue->updated_at->toISOString(),
                'images' => $venue->propertyImages->map(function ($image) use ($venue) {
                    return [
                        'loc' => asset(ltrim($image->location, '/')),
                        'caption' => $venue->venue_name . ' - ' . ($image->featured ? 'Main Image' : 'Gallery Image'),
                        'title' => $venue->venue_name
                    ];
                })
            ];
        }

        return $this->generateSitemap($urls);
    }

    /**
     * Generate XML sitemap from URLs array
     */
    private function generateSitemap($urls)
    {
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";

        foreach ($urls as $url) {
            $sitemap .= "  <url>\n";
            $sitemap .= "    <loc>" . htmlspecialchars($url['url']) . "</loc>\n";

            if (isset($url['lastmod'])) {
                $sitemap .= "    <lastmod>" . $url['lastmod'] . "</lastmod>\n";
            }

            $sitemap .= "    <changefreq>" . $url['changefreq'] . "</changefreq>\n";
            $sitemap .= "    <priority>" . $url['priority'] . "</priority>\n";

            // Add image sitemap data
            if (isset($url['images']) && $url['images']->count() > 0) {
                foreach ($url['images'] as $image) {
                    $sitemap .= "    <image:image>\n";
                    $sitemap .= "      <image:loc>" . htmlspecialchars($image['loc']) . "</image:loc>\n";
                    $sitemap .= "      <image:title>" . htmlspecialchars($image['title']) . "</image:title>\n";
                    $sitemap .= "      <image:caption>" . htmlspecialchars($image['caption']) . "</image:caption>\n";
                    $sitemap .= "    </image:image>\n";
                }
            }

            $sitemap .= "  </url>\n";
        }

        $sitemap .= '</urlset>';

        return response($sitemap)
            ->header('Content-Type', 'application/xml')
            ->header('Cache-Control', 'public, max-age=3600'); // Cache for 1 hour
    }

    /**
     * Helper method to add sitemap URL to index
     */
    private function addSitemapUrl($url, $lastmod)
    {
        return "  <sitemap>\n    <loc>" . $url . "</loc>\n    <lastmod>" . $lastmod->toISOString() . "</lastmod>\n  </sitemap>\n";
    }
}
