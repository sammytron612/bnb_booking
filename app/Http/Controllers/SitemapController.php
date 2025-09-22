<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venue;
use App\Models\Review;
use Carbon\Carbon;

class SitemapController extends Controller
{
    public function index()
    {
        $sitemaps = [
            [
                'loc' => route('sitemap.main'),
                'lastmod' => Carbon::now()->toISOString()
            ],
            [
                'loc' => route('sitemap.venues'),
                'lastmod' => Carbon::now()->toISOString()
            ]
        ];

        return response()->view('sitemaps.sitemap-index', compact('sitemaps'))
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
            ]
        ];

        return response()->view('sitemaps.urlset', compact('urls'))
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

        return response()->view('sitemaps.urlset', compact('urls'))
            ->header('Content-Type', 'application/xml');
    }
}
