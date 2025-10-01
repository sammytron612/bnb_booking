<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Review;
use App\Models\Venue;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.index');
    }
    public function bookings()
    {
        return view('admin.bookings');
    }

    public function refunds()
    {
        return view('admin.refunds-livewire');
    }

    public function reviews()
    {
        return view('admin.reviews');
    }

    public function analytics()
    {
        // Calculate total revenue
        $totalRevenue = Booking::where('status', 'confirmed')
            ->where('is_paid', true)
            ->sum('total_price');

        // Calculate revenue for last month for comparison
        $lastMonthRevenue = Booking::where('status', 'confirmed')
            ->where('is_paid', true)
            ->whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->sum('total_price');

        // Calculate revenue growth percentage
        $revenueGrowth = $lastMonthRevenue > 0
            ? round((($totalRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100)
            : 0;

        // Get total bookings
        $totalBookings = Booking::where('status', 'confirmed')->count();

        // Get last month bookings for comparison
        $lastMonthBookings = Booking::where('status', 'confirmed')
            ->whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->count();

        // Calculate booking growth percentage
        $bookingGrowth = $lastMonthBookings > 0
            ? round((($totalBookings - $lastMonthBookings) / $lastMonthBookings) * 100)
            : 0;

        // Calculate occupancy rate (simplified calculation)
        $totalPossibleNights = 30 * Venue::count(); // 30 days * number of venues
        $bookedNights = Booking::where('status', 'confirmed')
            ->whereMonth('check_in', Carbon::now()->month)
            ->sum('nights');
        $occupancyRate = $totalPossibleNights > 0
            ? round(($bookedNights / $totalPossibleNights) * 100)
            : 0;

        // Get average rating
        $averageRating = Review::avg('rating') ?: 0;
        $totalReviews = Review::count();

        // Get venue performance data
        $venues = Venue::withCount(['bookings' => function($query) {
                $query->where('status', 'confirmed');
            }])
            ->with(['bookings' => function($query) {
                $query->where('status', 'confirmed')
                      ->where('is_paid', true);
            }])
            ->get()
            ->map(function($venue) {
                $revenue = $venue->bookings->sum('total_price');
                return [
                    'id' => $venue->id,
                    'name' => $venue->venue_name,
                    'bookings_count' => $venue->bookings_count,
                    'revenue' => $revenue,
                    'route' => $venue->route
                ];
            })
            ->sortByDesc('revenue');

        // Calculate booking distribution percentages
        $totalVenueBookings = $venues->sum('bookings_count');
        $venueDistribution = $venues->map(function($venue) use ($totalVenueBookings) {
            $percentage = $totalVenueBookings > 0
                ? round(($venue['bookings_count'] / $totalVenueBookings) * 100)
                : 0;
            return array_merge($venue, ['percentage' => $percentage]);
        });

        // Get recent activity (last 10 bookings and reviews)
        $recentBookings = Booking::where('status', 'confirmed')
            ->with('venue')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $recentReviews = Review::with('booking.venue')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.analytics', compact(
            'totalRevenue',
            'revenueGrowth',
            'totalBookings',
            'bookingGrowth',
            'occupancyRate',
            'averageRating',
            'totalReviews',
            'venues',
            'venueDistribution',
            'recentBookings',
            'recentReviews'
        ));
    }

    public function properties()
    {
        return view('admin.properties');
    }
}
