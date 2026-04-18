<?php

namespace App\Http\Controllers\Api\Manager;

use App\Http\Controllers\Controller;
use App\Http\Requests\LinkWaiterRequest;
use App\Models\User;
use App\Models\WaiterRestaurantAssignment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WaiterController extends Controller
{
    /**
     * List waiters linked to manager's restaurant.
     */
    public function index(): JsonResponse
    {
        $restaurantId = Auth::user()->restaurant_id;
        $waiters = User::role('waiter')
            ->activeAtRestaurant($restaurantId)
            ->withCount('orders')
            ->orderBy('name')
            ->get()
            ->map(fn ($w) => [
                'id' => $w->id,
                'name' => $w->name,
                'global_waiter_number' => $w->global_waiter_number,
                'waiter_code' => $w->waiter_code,
                'employment_type' => $w->employment_type,
                'linked_until' => $w->linked_until?->format('Y-m-d'),
                'orders_count' => $w->orders_count,
                'profile_photo_url' => $w->profilePhotoUrl(),
            ]);

        return response()->json([
            'success' => true,
            'data' => $waiters,
        ]);
    }

    /**
     * Search waiter by unique code (global_waiter_number, e.g. TIPTAP-W-00001).
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate(['q' => 'required|string|max:30']);

        $q = strtoupper(trim($request->q));

        $waiter = User::role('waiter')
            ->where('global_waiter_number', $q)
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
                'orders_count' => $waiter->orders_count,
                'feedback_count' => $waiter->feedback_count,
                'current_restaurant' => $waiter->restaurant?->name,
                'is_linked' => (bool) $waiter->restaurant_id,
                'work_history' => $workHistory,
                'profile_photo_url' => $waiter->profilePhotoUrl(),
            ],
        ]);
    }

    /**
     * Link waiter to manager's restaurant. Waiter code is generated on link.
     */
    public function link(LinkWaiterRequest $request, User $waiter): JsonResponse
    {
        if (! $waiter->hasRole('waiter')) {
            return response()->json(['success' => false, 'message' => 'User si waiter.'], 422);
        }

        if ($waiter->restaurant_id !== null) {
            return response()->json([
                'success' => false,
                'message' => 'Waiter tayari ameunganishwa na restaurant nyingine. Manager wa restaurant ile anafaa kum-unlink kwanza.',
            ], 422);
        }

        $restaurant = Auth::user()->restaurant;
        if (! $restaurant || ! $restaurant->tag_prefix) {
            return response()->json([
                'success' => false,
                'message' => 'Restaurant yako haijaweka tag prefix. Wasiliana na msaada.',
            ], 422);
        }

        $waiter->restaurant_id = $restaurant->id;
        $waiter->waiter_code = $restaurant->generateWaiterCode();
        $waiter->employment_type = $request->validated('employment_type');
        $waiter->linked_until = $request->validated('employment_type') === 'temporary'
            ? $request->validated('linked_until')
            : null;
        $waiter->save();

        WaiterRestaurantAssignment::create([
            'user_id' => $waiter->id,
            'restaurant_id' => $restaurant->id,
            'linked_at' => now(),
            'employment_type' => $waiter->employment_type,
            'linked_until' => $waiter->linked_until,
        ]);

        $message = "Waiter {$waiter->name} ameunganishwa na restaurant yako. Code: {$waiter->waiter_code}";
        if ($waiter->employment_type === 'temporary' && $waiter->linked_until) {
            $message .= ' (muda mpaka '.$waiter->linked_until->format('d/m/Y').')';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'waiter_id' => $waiter->id,
                'name' => $waiter->name,
                'waiter_code' => $waiter->waiter_code,
                'employment_type' => $waiter->employment_type,
                'linked_until' => $waiter->linked_until?->format('Y-m-d'),
            ],
        ], 201);
    }

    /**
     * Unlink waiter from manager's restaurant.
     */
    public function unlink(User $waiter): JsonResponse
    {
        if ($waiter->restaurant_id !== Auth::user()->restaurant_id || ! $waiter->hasRole('waiter')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $name = $waiter->name;
        $restaurantId = $waiter->restaurant_id;
        $linkedAtFallback = $waiter->updated_at;

        $waiter->restaurant_id = null;
        $waiter->waiter_code = null;
        $waiter->employment_type = null;
        $waiter->linked_until = null;
        $waiter->save();

        $updated = WaiterRestaurantAssignment::query()
            ->where('user_id', $waiter->id)
            ->where('restaurant_id', $restaurantId)
            ->whereNull('unlinked_at')
            ->update(['unlinked_at' => now()]);

        if ($updated === 0) {
            WaiterRestaurantAssignment::create([
                'user_id' => $waiter->id,
                'restaurant_id' => $restaurantId,
                'linked_at' => $linkedAtFallback ?? now(),
                'unlinked_at' => now(),
                'employment_type' => null,
                'linked_until' => null,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => "{$name} ameondolewa kwenye restaurant yako. History yake (orders, ratings) imebaki. Anaweza kuungwa na restaurant nyingine.",
        ]);
    }

    /**
     * Assignment history (link/unlink) with optional filters.
     */
    public function history(Request $request): JsonResponse
    {
        $restaurantId = Auth::user()->restaurant_id;

        $this->ensureCurrentLinkedWaitersHaveAssignmentRecords($restaurantId);

        $query = WaiterRestaurantAssignment::query()
            ->where('restaurant_id', $restaurantId)
            ->with('user:id,name,global_waiter_number');

        $status = $request->string('status')->toString();
        if ($status === 'active') {
            $query->whereNull('unlinked_at');
        } elseif ($status === 'unlinked') {
            $query->whereNotNull('unlinked_at');
        }

        $search = $request->string('q')->trim();
        if ($search !== '') {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('global_waiter_number', 'like', '%'.strtoupper($search).'%');
            });
        }

        $dateFrom = $request->string('date_from')->trim();
        if ($dateFrom !== '') {
            $query->whereDate('linked_at', '>=', $dateFrom);
        }
        $dateTo = $request->string('date_to')->trim();
        if ($dateTo !== '') {
            $query->whereDate('linked_at', '<=', $dateTo);
        }

        $assignments = $query->orderByDesc('linked_at')->limit(200)->get()->map(fn ($a) => [
            'id' => $a->id,
            'waiter_id' => $a->user_id,
            'waiter_name' => $a->user?->name,
            'global_waiter_number' => $a->user?->global_waiter_number,
            'employment_type' => $a->employment_type,
            'linked_until' => $a->linked_until?->format('Y-m-d'),
            'linked_at' => $a->linked_at?->toIso8601String(),
            'unlinked_at' => $a->unlinked_at?->toIso8601String(),
            'is_active' => $a->unlinked_at === null,
        ]);

        return response()->json([
            'success' => true,
            'data' => $assignments,
        ]);
    }

    private function ensureCurrentLinkedWaitersHaveAssignmentRecords(int $restaurantId): void
    {
        $linkedUserIds = User::role('waiter')
            ->where('restaurant_id', $restaurantId)
            ->pluck('id');

        if ($linkedUserIds->isEmpty()) {
            return;
        }

        $existing = WaiterRestaurantAssignment::query()
            ->where('restaurant_id', $restaurantId)
            ->whereNull('unlinked_at')
            ->whereIn('user_id', $linkedUserIds)
            ->pluck('user_id');

        $missing = $linkedUserIds->diff($existing);

        foreach ($missing as $userId) {
            $user = User::find($userId);
            if (! $user) {
                continue;
            }
            WaiterRestaurantAssignment::create([
                'user_id' => $user->id,
                'restaurant_id' => $restaurantId,
                'linked_at' => $user->updated_at ?? now(),
                'unlinked_at' => null,
                'employment_type' => $user->employment_type,
                'linked_until' => $user->linked_until,
            ]);
        }
    }
}
