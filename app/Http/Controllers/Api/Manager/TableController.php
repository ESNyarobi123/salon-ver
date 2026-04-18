<?php

namespace App\Http\Controllers\Api\Manager;

use App\Http\Controllers\Controller;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TableController extends Controller
{
    /**
     * List all tables.
     */
    public function index()
    {
        $tables = Table::all();

        return response()->json([
            'success' => true,
            'data' => $tables,
        ]);
    }

    /**
     * Create a new table.
     */
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

        // Generate QR Code content (e.g., URL to order page with table ID)
        // You can customize this URL structure
        $qrContent = config('app.url').'/menu/'.$restaurant->id.'?table='.$table->id;

        // In a real app, you might generate and save the QR image to storage
        // For now, we'll just save the content string
        $table->update(['qr_code' => $qrContent]);

        return response()->json([
            'success' => true,
            'message' => 'Seat created successfully',
            'data' => $table,
        ], 201);
    }

    /**
     * Show a specific table.
     */
    public function show(Table $table)
    {
        return response()->json([
            'success' => true,
            'data' => $table,
        ]);
    }

    /**
     * Update a table.
     */
    public function update(Request $request, Table $table)
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'capacity' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $table->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Seat updated successfully',
            'data' => $table,
        ]);
    }

    /**
     * Delete a table.
     */
    public function destroy(Table $table)
    {
        $table->delete();

        return response()->json([
            'success' => true,
            'message' => 'Seat deleted successfully',
        ]);
    }
}
