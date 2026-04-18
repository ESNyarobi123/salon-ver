<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminSentNotification;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $restaurants = Restaurant::orderBy('name')->get();
        $recent = AdminSentNotification::with(['user', 'restaurant'])
            ->latest()
            ->limit(20)
            ->get();

        return view('admin.notifications.index', compact('restaurants', 'recent'));
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'target' => 'required|in:all,managers,waiters,specific_restaurant',
            'restaurant_id' => 'nullable|exists:restaurants,id',
        ], [
            'title.required' => 'Kichwa cha tangazo ni lazima.',
            'message.required' => 'Ujumbe ni lazima.',
        ]);

        if ($validated['target'] === 'specific_restaurant' && empty($validated['restaurant_id'])) {
            return back()
                ->withInput()
                ->withErrors(['restaurant_id' => 'Chagua saloon unapotuma kwa saloon maalum.']);
        }

        AdminSentNotification::create([
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'message' => $validated['message'],
            'target' => $validated['target'],
            'restaurant_id' => $validated['target'] === 'specific_restaurant' ? $validated['restaurant_id'] : null,
        ]);

        // TODO: Integrate real push (FCM/OneSignal) here

        return back()->with('success', 'Tangazo limepokelewa. Push notification litatumwa kwa watumiaji waliochaguliwa.');
    }
}
