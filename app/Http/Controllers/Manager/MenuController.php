<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    public function index()
    {
        $restaurantId = Auth::user()->restaurant_id;
        if ($restaurantId) {
            Restaurant::find($restaurantId)?->ensureDefaultCatalogCategories();
        }
        $categories = Category::where('restaurant_id', $restaurantId)->get();
        $menuItems = MenuItem::with('category')->where('restaurant_id', $restaurantId)->latest()->get();

        return view('manager.menu.index', compact('categories', 'menuItems'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'preparation_time' => 'nullable|integer|min:1',
        ]);

        $data = $request->all();
        $data['restaurant_id'] = Auth::user()->restaurant_id;
        $data['is_available'] = $request->has('is_available') ? 1 : 0;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('menu', 'public');
        }

        MenuItem::create($data);

        return back()->with('success', 'Service / product added successfully!');
    }

    public function update(Request $request, MenuItem $menuItem)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'preparation_time' => 'nullable|integer|min:1',
            'is_available' => 'boolean',
        ]);

        $data = $request->all();
        $data['is_available'] = $request->has('is_available') ? 1 : 0;

        if ($request->hasFile('image')) {
            if ($menuItem->image) {
                Storage::disk('public')->delete($menuItem->image);
            }
            $data['image'] = $request->file('image')->store('menu', 'public');
        }

        $menuItem->update($data);

        return back()->with('success', 'Service / product updated successfully!');
    }

    public function destroy(MenuItem $menuItem)
    {
        if ($menuItem->image) {
            Storage::disk('public')->delete($menuItem->image);
        }
        $menuItem->delete();

        return back()->with('success', 'Service / product deleted successfully!');
    }
}
