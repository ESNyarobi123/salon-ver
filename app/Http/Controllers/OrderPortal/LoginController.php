<?php

namespace App\Http\Controllers\OrderPortal;

use App\Http\Controllers\Controller;
use App\Models\OrderPortalPassword;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function create(): View
    {
        return view('order-portal.login');
    }

    /**
     * Login with password only. Password is unique per waiter/restaurant;
     * system identifies which restaurant (and waiter) from the password.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $request->validate([
            'password' => 'required|string|max:50',
        ]);

        $plain = $request->password;

        $credential = OrderPortalPassword::query()
            ->whereNull('revoked_at')
            ->with(['user', 'restaurant'])
            ->get()
            ->first(fn ($c) => $c->checkPassword($plain));

        if (! $credential) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Password si sahihi au imekwisha tamaa. Omba mpya kwa manager wako.',
                ], 422);
            }

            return back()->with('error', 'Password si sahihi au imekwisha tamaa. Omba mpya kwa manager wako.');
        }

        $user = $credential->user;
        if (! $user->hasRole('waiter') || $user->restaurant_id != $credential->restaurant_id) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Huna ufikiaji wa service desk. Wasiliana na manager wako.',
                ], 403);
            }

            return back()->with('error', 'Huna ufikiaji wa service desk. Wasiliana na manager wako.');
        }

        session([
            'order_portal_restaurant_id' => $credential->restaurant_id,
            'order_portal_user_id' => $user->id,
        ]);

        if ($request->expectsJson()) {
            $token = Str::random(64);
            Cache::put('order_portal_token:'.$token, [
                'restaurant_id' => $credential->restaurant_id,
                'user_id' => $user->id,
            ], now()->addDays(30));

            return response()->json([
                'success' => true,
                'message' => 'Umefanikiwa kuingia.',
                'data' => [
                    'token' => $token,
                    'restaurant_id' => $credential->restaurant_id,
                    'restaurant_name' => $credential->restaurant?->name,
                    'user_id' => $user->id,
                    'user_name' => $user->name ?? null,
                ],
            ]);
        }

        return redirect()->route('order-portal.orders')->with('success', 'Umefanikiwa kuingia.');
    }

    public function destroy(Request $request): RedirectResponse|JsonResponse
    {
        $bearer = $request->bearerToken();
        if ($bearer) {
            Cache::forget('order_portal_token:'.$bearer);
        }
        session()->forget(['order_portal_restaurant_id', 'order_portal_user_id']);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Umetoka.',
            ]);
        }

        return redirect()->route('order-portal.login')->with('success', 'Umetoka.');
    }
}
