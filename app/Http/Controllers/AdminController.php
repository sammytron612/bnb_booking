<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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

    public function reviews()
    {
        return view('admin.reviews');
    }

    public function analytics()
    {
        return view('admin.analytics');
    }

    public function properties()
    {
        return view('admin.properties');
    }
}
