<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RestaurantRegistrationController extends Controller
{
    public function create()
    {
        return view('auth.register-restaurant');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'restaurant_name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'manager_name' => 'required|string|max:255',
            'manager_email' => 'required|email|unique:users,email',
            'manager_password' => 'required|confirmed|min:8',
        ]);

        // 1. Create Restaurant
        $restaurant = Restaurant::create([
            'name' => $validated['restaurant_name'],
            'location' => $validated['location'],
            'phone' => $validated['phone'],
            'is_active' => true,
        ]);

        // 2. Create Manager
        $manager = User::create([
            'name' => $validated['manager_name'],
            'email' => $validated['manager_email'],
            'password' => Hash::make($validated['manager_password']),
            'restaurant_id' => $restaurant->id,
        ]);

        $manager->assignRole('manager');

        // 3. Auto-login manager
        Auth::login($manager);

        return redirect()->route('manager.dashboard')->with('status', config('salon.entity').' created successfully! Please set up your '.strtolower(config('salon.service_catalog_title')).'.');
    }
}
