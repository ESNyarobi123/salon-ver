<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = \App\Models\Order::with(['restaurant', 'items.menuItem'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('restaurant_id')) {
            $query->where('restaurant_id', $request->restaurant_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($qry) use ($q) {
                $qry->where('id', 'like', '%'.$q.'%')
                    ->orWhere('customer_name', 'like', '%'.$q.'%')
                    ->orWhere('customer_phone', 'like', '%'.$q.'%')
                    ->orWhere('payment_reference', 'like', '%'.$q.'%');
            });
        }

        $orders = $query->paginate(20)->withQueryString();
        $restaurants = \App\Models\Restaurant::orderBy('name')->get(['id', 'name']);

        return view('admin.orders.index', compact('orders', 'restaurants'));
    }

    public function export(Request $request): StreamedResponse
    {
        $query = \App\Models\Order::with('restaurant')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('restaurant_id')) {
            $query->where('restaurant_id', $request->restaurant_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($qry) use ($q) {
                $qry->where('id', 'like', '%'.$q.'%')
                    ->orWhere('customer_name', 'like', '%'.$q.'%')
                    ->orWhere('customer_phone', 'like', '%'.$q.'%')
                    ->orWhere('payment_reference', 'like', '%'.$q.'%');
            });
        }

        $orders = $query->limit(10000)->get();
        $filename = 'orders-export-'.now()->format('Y-m-d-His').'.csv';

        return response()->streamDownload(function () use ($orders): void {
            $out = fopen('php://output', 'w');
            fputcsv($out, [
                config('salon.booking').' ID',
                config('salon.entity'),
                'Amount',
                'Status',
                config('salon.customer'),
                'Phone',
                'Date',
            ]);
            foreach ($orders as $o) {
                fputcsv($out, [
                    '#'.str_pad($o->id, 6, '0', STR_PAD_LEFT),
                    $o->restaurant?->name ?? '',
                    $o->total_amount,
                    $o->status,
                    $o->customer_name ?? '',
                    $o->customer_phone ?? '',
                    $o->created_at->format('Y-m-d H:i'),
                ]);
            }
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    public function show(string $id)
    {
        $order = \App\Models\Order::with(['restaurant', 'items.menuItem', 'payment', 'waiter'])->findOrFail($id);

        return view('admin.orders.show', compact('order'));
    }

    public function edit(string $id)
    {
        $order = \App\Models\Order::findOrFail($id);

        return view('admin.orders.edit', compact('order'));
    }

    public function update(Request $request, string $id)
    {
        $order = \App\Models\Order::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,preparing,ready,completed,cancelled',
        ]);

        $order->update($validated);

        return redirect()->route('admin.orders.index')->with('success', 'Booking status updated successfully.');
    }

    public function destroy(string $id)
    {
        $order = \App\Models\Order::findOrFail($id);
        $order->delete();

        return redirect()->route('admin.orders.index')->with('success', 'Booking deleted successfully.');
    }
}
