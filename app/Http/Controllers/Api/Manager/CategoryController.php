<?php

namespace App\Http\Controllers\Api\Manager;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    /**
     * List all categories for the authenticated manager's restaurant.
     */
    public function index()
    {
        $categories = Category::orderBy('sort_order')->get()->map(function ($category) {
            $category->imageUrl = $category->imageUrl();

            return $category;
        });

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    /**
     * Create a new category.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'sort_order' => 'nullable|integer',
        ]);

        $data = $request->all();
        $data['restaurant_id'] = Auth::user()->restaurant_id;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        $category = Category::create($data);
        $category->imageUrl = $category->imageUrl();

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully',
            'data' => $category,
        ], 201);
    }

    /**
     * Show a specific category.
     */
    public function show(Category $category)
    {
        $category->imageUrl = $category->imageUrl();

        return response()->json([
            'success' => true,
            'data' => $category,
        ]);
    }

    /**
     * Update a category.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'sort_order' => 'nullable|integer',
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($data);
        $category->imageUrl = $category->imageUrl();

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully',
            'data' => $category,
        ]);
    }

    /**
     * Delete a category.
     */
    public function destroy(Category $category)
    {
        if ($category->menuItems()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete category that still has services or products.',
            ], 400);
        }

        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully',
        ]);
    }
}
