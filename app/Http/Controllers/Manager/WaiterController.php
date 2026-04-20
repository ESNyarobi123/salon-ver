<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Requests\LinkWaiterRequest;
use App\Models\OrderPortalPassword;
use App\Models\User;
use App\Models\WaiterRestaurantAssignment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WaiterController extends Controller
{
    public function index()
    {
        $restaurantId = Auth::user()->restaurant_id;
        $waiters = User::role('waiter')
            ->activeAtRestaurant($restaurantId)
            ->withCount('orders')
            ->get();

        $waiterIdsWithOrderPortal = OrderPortalPassword::query()
            ->where('restaurant_id', $restaurantId)
            ->whereNull('revoked_at')
            ->pluck('user_id')
            ->all();

        $orderPortalLoginUrl = route('order-portal.login');

        return view('manager.waiters.index', compact('waiters', 'waiterIdsWithOrderPortal', 'orderPortalLoginUrl'));
    }

    /**
     * History page: link/unlink events with filters.
     */
    public function history(Request $request)
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

        $assignmentHistory = $query->orderByDesc('linked_at')->limit(200)->get();

        return view('manager.waiters.history', [
            'assignmentHistory' => $assignmentHistory,
            'filters' => [
                'status' => $status,
                'q' => $search,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ],
        ]);
    }

    /**
     * Search waiter by global number (for linking).
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate(['q' => 'required|string|max:30']);

        $q = User::normalizeGlobalWaiterNumberForLookup($request->q);

        $waiter = User::role('waiter')
            ->where('global_waiter_number', $q)
            ->withCount(['orders', 'feedback'])
            ->with('restaurant:id,name')
            ->first();

        if (! $waiter) {
            return response()->json(['success' => false, 'message' => config('salon.stylist_lookup_failed_sw')]);
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
            'waiter' => [
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
     * Link waiter to current manager's restaurant (permanent or temporary / show-time).
     */
    public function link(LinkWaiterRequest $request, User $waiter): RedirectResponse
    {
        if (! $waiter->hasRole('waiter')) {
            return back()->with('error', 'Mtumiaji huyu si stylist.');
        }

        if ($waiter->restaurant_id !== null) {
            return back()->with('error', 'Stylist tayari ameunganishwa na saloon nyingine. Manager wa saloon ile anafaa kum-unlink kwanza.');
        }

        $restaurant = Auth::user()->restaurant;
        if (! $restaurant || ! $restaurant->tag_prefix) {
            return back()->with('error', 'Saloon yako haijaweka tag prefix. Wasiliana na msaada.');
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

        $msg = "Stylist {$waiter->name} ameunganishwa na saloon yako. Code: {$waiter->waiter_code}";
        if ($waiter->employment_type === 'temporary' && $waiter->linked_until) {
            $msg .= ' (muda mpaka '.$waiter->linked_until->format('d/m/Y').')';
        }

        return back()->with('success', $msg);
    }

    /**
     * Unlink waiter from current manager's restaurant (history is preserved).
     */
    public function unlink(User $waiter): RedirectResponse
    {
        if ($waiter->restaurant_id !== Auth::user()->restaurant_id || ! $waiter->hasRole('waiter')) {
            return back()->with('error', 'Unauthorized.');
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

        OrderPortalPassword::query()
            ->where('user_id', $waiter->id)
            ->where('restaurant_id', $restaurantId)
            ->whereNull('revoked_at')
            ->update(['revoked_at' => now()]);

        return back()->with('success', "{$name} ameondolewa kwenye saloon yako. History yake (bookings, ratings) imebaki. Anaweza kuungwa na saloon nyingine.");
    }

    /**
     * Generate or regenerate Order Portal password for a linked waiter.
     * Password is shown once; when waiter is unlinked it is revoked.
     * One row per (restaurant_id, user_id): update if exists, create if not.
     */
    public function generateOrderPortalPassword(User $waiter): RedirectResponse
    {
        if ($waiter->restaurant_id !== Auth::user()->restaurant_id || ! $waiter->hasRole('waiter')) {
            return back()->with('error', 'Unauthorized.');
        }

        $plainPassword = OrderPortalPassword::generateRandomPassword();

        $existing = OrderPortalPassword::query()
            ->where('restaurant_id', $waiter->restaurant_id)
            ->where('user_id', $waiter->id)
            ->first();

        if ($existing) {
            $existing->update([
                'password' => $plainPassword,
                'generated_at' => now(),
                'revoked_at' => null,
            ]);
        } else {
            OrderPortalPassword::create([
                'restaurant_id' => $waiter->restaurant_id,
                'user_id' => $waiter->id,
                'password' => $plainPassword,
                'generated_at' => now(),
            ]);
        }

        return back()
            ->with('success', 'Nenosiri la service desk limetengenezwa. Mwambie stylist nambari yake ya pekee na nenosiri hili.')
            ->with('order_portal_password_generated', $plainPassword)
            ->with('order_portal_waiter_name', $waiter->name)
            ->with('order_portal_waiter_number', $waiter->global_waiter_number);
    }

    /**
     * Backfill: waiters currently linked to this restaurant but without an assignment
     * record (e.g. linked before the feature) get one so they appear in history.
     */
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
