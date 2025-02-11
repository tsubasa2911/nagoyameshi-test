<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $highly_rated_restaurants = Restaurant::all()->take(6);
        $categories = Category::all();
        $new_restaurants = Restaurants::orderBy('created_at', 'desc')->take(6);

        return view('views.home', compact('highly_rated_restaurants', 'categories', 'new_restaurants'));
    }
}
