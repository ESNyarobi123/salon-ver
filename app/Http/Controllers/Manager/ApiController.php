<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Services\SelcomService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiController extends Controller
{
    protected SelcomService $selcom;

    public function __construct(SelcomService $selcom)
    {
        $this->selcom = $selcom;
    }

    public function index()
    {
        $restaurant = Auth::user()->restaurant;

        return view('manager.api.index', compact('restaurant'));
    }

    /**
     * Update Selcom Payment Gateway Credentials
     */
    public function updateSelcomCredentials(Request $request)
    {
        $request->validate([
            'selcom_vendor_id' => 'required|string|max:255',
            'selcom_api_key' => 'required|string|max:255',
            'selcom_api_secret' => 'required|string|max:255',
            'selcom_is_live' => 'nullable|boolean',
        ]);

        $restaurant = Auth::user()->restaurant;
        $restaurant->update([
            'selcom_vendor_id' => $request->selcom_vendor_id,
            'selcom_api_key' => $request->selcom_api_key,
            'selcom_api_secret' => $request->selcom_api_secret,
            'selcom_is_live' => $request->has('selcom_is_live'),
        ]);

        return back()->with('success', 'Selcom credentials updated successfully!');
    }

    /**
     * Update Customer Support Phone (shown on WhatsApp bot)
     */
    public function updateSupportPhone(Request $request)
    {
        $request->validate([
            'support_phone' => 'nullable|string|max:20',
        ]);

        $restaurant = Auth::user()->restaurant;
        $restaurant->update([
            'support_phone' => $request->input('support_phone') ?: null,
        ]);

        return back()->with('success', 'Customer support number updated. It will appear on the WhatsApp menu when set.');
    }

    /**
     * Guest Wi‑Fi (SSID + password) shown to customers on the WhatsApp bot.
     */
    public function updateGuestWifi(Request $request)
    {
        $request->validate([
            'guest_wifi_ssid' => 'nullable|string|max:100',
            'guest_wifi_password' => 'nullable|string|max:100',
        ]);

        $restaurant = Auth::user()->restaurant;
        $restaurant->update([
            'guest_wifi_ssid' => $request->filled('guest_wifi_ssid') ? trim((string) $request->input('guest_wifi_ssid')) : null,
            'guest_wifi_password' => $request->filled('guest_wifi_password') ? trim((string) $request->input('guest_wifi_password')) : null,
        ]);

        return back()->with('success', 'Guest Wi-Fi details saved. Customers can open “WiFi password” on the bot to see them.');
    }

    /**
     * Test Selcom Connection
     */
    public function testSelcomConnection()
    {
        $restaurant = Auth::user()->restaurant;

        if (! $restaurant->hasSelcomConfigured()) {
            return response()->json([
                'success' => false,
                'message' => 'Please save your Selcom credentials first',
            ]);
        }

        $credentials = $restaurant->getSelcomCredentials();

        // Try to validate credentials by making a test call
        // We'll create a minimal order to test the connection
        $testOrderId = 'TEST-'.time();

        try {
            $result = $this->selcom->checkOrderStatus($credentials, $testOrderId);

            // A 404 "not found" is actually good - it means credentials are valid
            // but the order doesn't exist (which is expected for a test order)
            if (isset($result['resultcode'])) {
                if ($result['resultcode'] === '404' || $result['resultcode'] === '000') {
                    return response()->json([
                        'success' => true,
                        'message' => 'Connection successful! Selcom credentials are valid. Mode: '.
                            ($credentials['is_live'] ? 'LIVE' : 'TEST'),
                    ]);
                }

                // Authentication errors
                if ($result['resultcode'] === '401' || $result['resultcode'] === '403') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid credentials. Please check your API Key and Secret.',
                    ]);
                }
            }

            // Check for error messages in the response
            if (isset($result['message']) && str_contains(strtolower($result['message']), 'unauthorized')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials. Please check your API Key and Secret.',
                ]);
            }

            // If we got a response, credentials are likely valid
            return response()->json([
                'success' => true,
                'message' => 'Connection successful! Mode: '.($credentials['is_live'] ? 'LIVE' : 'TEST'),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Connection failed: '.$e->getMessage(),
            ]);
        }
    }
}
