<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
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
            ->orderBy('category_id')
            ->orderBy('name')
            ->get();

        $lowStockItems = $menuItems->filter(fn (MenuItem $item) => $item->stock_tracked && $item->isLowStock());
        $trackedCount = $menuItems->where('stock_tracked', true)->count();

        return view('manager.stock.index', compact('menuItems', 'lowStockItems', 'trackedCount'));
    }

    public function update(Request $request, MenuItem $menuItem)
    {
        if ($menuItem->restaurant_id !== Auth::user()->restaurant_id) {
            abort(403);
        }

        $request->validate([
            'stock_quantity' => 'required|integer|min:0',
            'low_stock_threshold' => 'required|integer|min:0',
        ]);

        $menuItem->update([
            'stock_tracked' => $request->boolean('stock_tracked'),
            'stock_quantity' => (int) $request->input('stock_quantity'),
            'low_stock_threshold' => (int) $request->input('low_stock_threshold'),
        ]);

        return back()->with('success', 'Stock settings saved for '.$menuItem->name.'.');
    }
}
