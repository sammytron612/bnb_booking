<?php

namespace App\Http\Controllers;


use App\Models\Venue;
use Carbon\Carbon;

class SitemapController extends Controller
{
    public function index()
    {
        $sitemaps = [
            [
                'loc' => route('api.sitemap.main'),
                'lastmod' => Carbon::now()->toISOString()
            ],
            [
                'loc' => route('api.sitemap.venues'),
                'lastmod' => Carbon::now()->toISOString()
            ]
        ];

        $content = '<?xml version="1.0" encoding="UTF-8"?>' . view('sitemaps.sitemap-index', compact('sitemaps'))->render();

        return response($content)
            ->header('Content-Type', 'application/xml');
    }

    public function main()
    {
        $urls = [
            [
                'loc' => route('home'),
                'lastmod' => Carbon::now()->toISOString(),
                'changefreq' => 'weekly',
                'priority' => '1.0'
            ],
            [
                'loc' => route('contact'),
                'lastmod' => Carbon::now()->toISOString(),
                'changefreq' => 'monthly',
                'priority' => '0.8'
            ],
            [
                'loc' => route('seaglass'),
                'lastmod' => Carbon::now()->toISOString(),
                'changefreq' => 'monthly',
                'priority' => '0.7'
            ]
        ];

        $content = '<?xml version="1.0" encoding="UTF-8"?>' . view('sitemaps.urlset', compact('urls'))->render();

        return response($content)
            ->header('Content-Type', 'application/xml');
    }

    public function venues()
    {
        $venues = Venue::all();
        $urls = [];

        foreach ($venues as $venue) {
            $lastmod = Carbon::now()->toISOString(); // You could use updated_at if available

            $urls[] = [
                'loc' => route('venue.show', $venue->route),
                'lastmod' => $lastmod,
                'changefreq' => 'weekly',
                'priority' => '0.8',
                'images' => $venue->propertyImages->map(function ($image) use ($venue) {
                    return [
                        'loc' => asset(ltrim($image->location, '/')),
                        'caption' => $venue->venue_name . ' - ' . ($image->featured ? 'Main Image' : 'Gallery Image'),
                        'title' => $venue->venue_name
                    ];
                })
            ];
        }

        $content = '<?xml version="1.0" encoding="UTF-8"?>' . view('sitemaps.urlset', compact('urls'))->render();

        return response($content)
            ->header('Content-Type', 'application/xml');
    }
}
