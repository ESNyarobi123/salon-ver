<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\Payment::with(['order.restaurant'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $payments = $query->paginate(20)->withQueryString();

        return view('admin.payments.index', compact('payments'));
    }

    public function export(Request $request): StreamedResponse
    {
        $query = \App\Models\Payment::with(['order.restaurant'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $payments = $query->limit(10000)->get();
        $filename = 'payments-export-'.now()->format('Y-m-d-His').'.csv';

        return response()->streamDownload(function () use ($payments): void {
            $out = fopen('php://output', 'w');
            fputcsv($out, [
                'Transaction ID',
                config('salon.booking').' ID',
                config('salon.entity'),
                'Amount',
                'Method',
                'Status',
                'Date',
            ]);
            foreach ($payments as $p) {
                fputcsv($out, [
                    $p->transaction_id ?? '',
                    $p->order_id,
                    $p->order?->restaurant?->name ?? '',
                    $p->amount,
                    $p->payment_method ?? '',
                    $p->status,
                    $p->created_at->format('Y-m-d H:i'),
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
        $payment = \App\Models\Payment::with(['order.restaurant'])->findOrFail($id);

        return view('admin.payments.show', compact('payment'));
    }
}
