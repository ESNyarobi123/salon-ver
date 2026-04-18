<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Table;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TableController extends Controller
{
    public function index()
    {
        $restaurantId = Auth::user()->restaurant_id;
        $tables = Table::with('waiter')->where('restaurant_id', $restaurantId)->latest()->get();
        $waiters = User::role('waiter')->where('restaurant_id', $restaurantId)->orderBy('name')->get(['id', 'name']);

        return view('manager.tables.index', compact('tables', 'waiters'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'capacity' => 'nullable|integer|min:1',
        ]);

        $restaurant = Auth::user()->restaurant;

        $table = Table::create([
            'restaurant_id' => $restaurant->id,
            'name' => $request->name,
            'capacity' => $request->capacity ?? 4,
            'is_active' => true,
        ]);

        // Generate QR Code content URL (WhatsApp format)
        $table->update(['qr_code' => $table->whatsapp_qr_url]);

        return back()->with('success', 'Seat created successfully!');
    }

    public function update(Request $request, Table $table)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'capacity' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'waiter_id' => 'nullable|exists:users,id',
        ]);

        $data = $request->only(['name', 'capacity', 'waiter_id']);
        $data['is_active'] = $request->boolean('is_active');

        $table->update($data);

        return back()->with('success', 'Seat updated successfully!');
    }

    public function destroy(Table $table)
    {
        $table->delete();

        return back()->with('success', 'Seat deleted successfully!');
    }
}
