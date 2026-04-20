<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WaiterRestaurantAssignment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WaiterController extends Controller
{
    /**
     * List all waiters with unique codes. Optional search by name or unique code.
     */
    public function index(Request $request): View
    {
        $query = User::role('waiter')
            ->with('restaurant:id,name')
            ->withCount('orders')
            ->orderBy('name');

        $q = $request->string('q')->trim();
        if ($q !== '') {
            $codeLike = strtoupper($q);
            $query->where(function ($builder) use ($q, $codeLike) {
                $builder->where('name', 'like', '%'.$q.'%')
                    ->orWhere('email', 'like', '%'.$q.'%')
                    ->orWhere('global_waiter_number', 'like', '%'.$codeLike.'%')
                    ->orWhere('waiter_code', 'like', '%'.$codeLike.'%');
            });
        }

        $waiters = $query->paginate(20)->withQueryString();

        return view('admin.waiters.index', [
            'waiters' => $waiters,
            'search' => $q,
        ]);
    }

    /**
     * Search waiter by unique code (8-hex, legacy 4-digit, or TIPTAP-W-xxxxx). Returns JSON like manager search.
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate(['q' => 'required|string|max:30']);

        $code = User::normalizeGlobalWaiterNumberForLookup($request->q);

        $waiter = User::role('waiter')
            ->where('global_waiter_number', $code)
            ->withCount(['orders', 'feedback'])
            ->with('restaurant:id,name')
            ->first();

        if (! $waiter) {
            return response()->json([
                'success' => false,
                'message' => config('salon.stylist_lookup_failed_sw'),
            ], 404);
        }

        $workHistory = WaiterRestaurantAssignment::query()
            ->where('user_id', $waiter->id)
            ->with('restaurant:id,name')
            ->orderByDesc('linked_at')
            ->get()
            ->map(fn ($a) => [
                'restaurant_name' => $a->restaurant?->name ?? '—',
                'linked_at' => $a->linked_at?->toIso8601String(),
                'unlinked_at' => $a->unlinked_at?->toIso8601String(),
                'employment_type' => $a->employment_type,
                'linked_until' => $a->linked_until?->format('Y-m-d'),
                'is_active' => $a->unlinked_at === null,
            ]);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $waiter->id,
                'name' => $waiter->name,
                'email' => $waiter->email,
                'phone' => $waiter->phone,
                'location' => $waiter->location,
                'global_waiter_number' => $waiter->global_waiter_number,
                'waiter_code' => $waiter->waiter_code,
                'orders_count' => $waiter->orders_count,
                'feedback_count' => $waiter->feedback_count,
                'current_restaurant' => $waiter->restaurant?->name,
                'is_linked' => (bool) $waiter->restaurant_id,
                'work_history' => $workHistory,
                'profile_photo_url' => $waiter->profilePhotoUrl(),
            ],
        ]);
    }
}
