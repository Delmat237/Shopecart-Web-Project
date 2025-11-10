<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $featuredProducts = Product::with('category')
            ->visible()
            ->featured()
            ->inStock()
            ->latest()
            ->take(8)
            ->get();

        return view('pages.home', compact('featuredProducts'));
    }

    public function about()
    {
        return view('pages.about');
    }

    public function contact()
    {
        return view('pages.contact');
    }
}