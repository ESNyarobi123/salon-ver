<?php

namespace App\Http\Controllers\Api\Manager;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

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
            'catalog_kind' => ['nullable', Rule::in([Category::CATALOG_KIND_SERVICE, Category::CATALOG_KIND_PRODUCT])],
        ]);

        $data = $request->only(['name', 'sort_order']);
        $data['restaurant_id'] = Auth::user()->restaurant_id;
        $data['catalog_kind'] = $request->input('catalog_kind', Category::CATALOG_KIND_SERVICE);

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
            'catalog_kind' => ['sometimes', 'required', Rule::in([Category::CATALOG_KIND_SERVICE, Category::CATALOG_KIND_PRODUCT])],
        ]);

        $data = collect($request->only(['name', 'sort_order', 'catalog_kind']))->filter(fn ($v) => $v !== null)->all();

        if ($request->hasFile('image')) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($data);

        if ($category->wasChanged('catalog_kind')) {
            $category->menuItems()->update(['stock_tracked' => $category->isProductCatalog()]);
        }
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
