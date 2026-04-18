<?php

namespace App\Http\Controllers\Api\Manager;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    /**
     * List all menu items.
     */
    public function index()
    {
        $menuItems = MenuItem::with('category')->latest()->get()->map(function ($item) {
            $item->imageUrl = $item->imageUrl();

            return $item;
        });

        return response()->json([
            'success' => true,
            'data' => $menuItems,
        ]);
    }

    /**
     * Create a new menu item.
     */
    public function store(Request $request)
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
        $data['restaurant_id'] = Auth::user()->restaurant_id;
        // Handle boolean conversion explicitly for API JSON input or form-data
        $data['is_available'] = filter_var($request->input('is_available', true), FILTER_VALIDATE_BOOLEAN);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('menu', 'public');
        }

        $menuItem = MenuItem::create($data);
        $menuItem->imageUrl = $menuItem->imageUrl();

        return response()->json([
            'success' => true,
            'message' => 'Service / product created successfully',
            'data' => $menuItem,
        ], 201);
    }

    /**
     * Show a specific menu item.
     */
    public function show(MenuItem $menuItem)
    {
        $menuItem->load('category');
        $menuItem->imageUrl = $menuItem->imageUrl();

        return response()->json([
            'success' => true,
            'data' => $menuItem,
        ]);
    }

    /**
     * Update a menu item.
     */
    public function update(Request $request, MenuItem $menuItem)
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'category_id' => 'sometimes|required|exists:categories,id',
            'price' => 'sometimes|required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'preparation_time' => 'nullable|integer|min:1',
            'is_available' => 'boolean',
        ]);

        $data = $request->all();

        if ($request->has('is_available')) {
            $data['is_available'] = filter_var($request->input('is_available'), FILTER_VALIDATE_BOOLEAN);
        }

        if ($request->hasFile('image')) {
            if ($menuItem->image) {
                Storage::disk('public')->delete($menuItem->image);
            }
            $data['image'] = $request->file('image')->store('menu', 'public');
        }

        $menuItem->update($data);
        $menuItem->imageUrl = $menuItem->imageUrl();

        return response()->json([
            'success' => true,
            'message' => 'Service / product updated successfully',
            'data' => $menuItem,
        ]);
    }

    /**
     * Delete a menu item.
     */
    public function destroy(MenuItem $menuItem)
    {
        if ($menuItem->image) {
            Storage::disk('public')->delete($menuItem->image);
        }
        $menuItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Service / product deleted successfully',
        ]);
    }
}
