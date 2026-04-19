<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'catalog_kind' => ['nullable', Rule::in([Category::CATALOG_KIND_SERVICE, Category::CATALOG_KIND_PRODUCT])],
        ]);

        $data = [
            'name' => $request->input('name'),
            'restaurant_id' => Auth::user()->restaurant_id,
            'catalog_kind' => $request->input('catalog_kind', Category::CATALOG_KIND_SERVICE),
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        Category::create($data);

        return back()->with('success', 'Category added successfully!');
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'catalog_kind' => ['required', Rule::in([Category::CATALOG_KIND_SERVICE, Category::CATALOG_KIND_PRODUCT])],
        ]);

        $data = [
            'name' => $request->input('name'),
            'catalog_kind' => $request->input('catalog_kind'),
        ];

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

        return back()->with('success', 'Category updated successfully!');
    }

    public function destroy(Category $category)
    {
        if ($category->menuItems()->count() > 0) {
            return back()->with('error', 'Cannot delete category with menu items.');
        }

        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }
        $category->delete();

        return back()->with('success', 'Category deleted successfully!');
    }
}
