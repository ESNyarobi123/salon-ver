<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * Menu image: upload saves to storage/app/public/menu_images.
 * Fetch URL via Restaurant::menuImageUrl() (storage.serve or asset).
 */
class MenuImageController extends Controller
{
    /**
     * Show the menu image management page.
     */
    public function index()
    {
        $restaurant = Restaurant::find(Auth::user()->restaurant_id);

        return view('manager.menu-image.index', compact('restaurant'));
    }

    /**
     * Upload/Update the menu image.
     */
    public function store(Request $request)
    {
        $request->validate([
            'menu_image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // Max 5MB
        ]);

        $restaurant = Restaurant::find(Auth::user()->restaurant_id);

        if (! $restaurant) {
            return back()->with('error', 'Restaurant not found.');
        }

        // Delete old image if exists
        if ($restaurant->menu_image) {
            Storage::disk('public')->delete($restaurant->menu_image);
        }

        // Path: storage/app/public/menu_images/{filename}
        $path = $request->file('menu_image')->store('menu_images', 'public');
        $restaurant->update(['menu_image' => $path]);

        return back()->with('success', 'Service menu image uploaded successfully!');
    }

    /**
     * Delete the menu image.
     */
    public function destroy()
    {
        $restaurant = Restaurant::find(Auth::user()->restaurant_id);

        if (! $restaurant) {
            return back()->with('error', 'Restaurant not found.');
        }

        if ($restaurant->menu_image) {
            Storage::disk('public')->delete($restaurant->menu_image);
            $restaurant->update(['menu_image' => null]);
        }

        return back()->with('success', 'Service menu image deleted successfully!');
    }
}
