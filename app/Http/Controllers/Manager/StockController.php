<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockController extends Controller
{
    public function index()
    {
        $restaurantId = Auth::user()->restaurant_id;

        $menuItems = MenuItem::with('category')
            ->where('restaurant_id', $restaurantId)
            ->whereHas('category', fn ($q) => $q->where('catalog_kind', Category::CATALOG_KIND_PRODUCT))
            ->orderBy('category_id')
            ->orderBy('name')
            ->get();

        $lowStockItems = $menuItems->filter(fn (MenuItem $item) => $item->stock_tracked && $item->isLowStock());
        $trackedCount = $menuItems->where('stock_tracked', true)->count();

        $outOfStockCount = $menuItems->filter(fn (MenuItem $item) => $item->isOutOfStock())->count();
        $lowStockCount = $lowStockItems->count();
        $healthyCount = $menuItems->filter(fn (MenuItem $item) => $item->stock_tracked && ! $item->isLowStock())->count();
        $totalLines = $menuItems->count();
        $overallStatus = $totalLines === 0
            ? 'empty'
            : ($lowStockCount === 0 ? 'healthy' : 'attention');

        return view('manager.stock.index', compact(
            'menuItems',
            'lowStockItems',
            'trackedCount',
            'outOfStockCount',
            'lowStockCount',
            'healthyCount',
            'totalLines',
            'overallStatus'
        ));
    }

    public function update(Request $request, MenuItem $menuItem)
    {
        if ($menuItem->restaurant_id !== Auth::user()->restaurant_id) {
            abort(403);
        }

        $menuItem->load('category');
        if (! $menuItem->category?->isProductCatalog()) {
            abort(404);
        }

        $request->validate([
            'stock_quantity' => 'required|integer|min:0',
            'low_stock_threshold' => 'required|integer|min:0',
        ]);

        $menuItem->update([
            'stock_tracked' => true,
            'stock_quantity' => (int) $request->input('stock_quantity'),
            'low_stock_threshold' => (int) $request->input('low_stock_threshold'),
        ]);

        return back()->with('success', 'Stock saved for '.$menuItem->name.'.');
    }
}
