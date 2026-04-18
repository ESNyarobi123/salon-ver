<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    protected $fillable = [
        'name',
        'location',
        'phone',
        'support_phone',
        'guest_wifi_ssid',
        'guest_wifi_password',
        'logo',
        'menu_image',
        'is_active',
        'tag_prefix',
        'selcom_vendor_id',
        'selcom_api_key',
        'selcom_api_secret',
        'selcom_is_live',
        'kitchen_token',
        'kitchen_token_generated_at',
    ];

    /**
     * Boot method - auto-generate tag_prefix on create
     */
    protected static function booted()
    {
        static::creating(function ($restaurant) {
            if (empty($restaurant->tag_prefix)) {
                $restaurant->tag_prefix = $restaurant->generateUniqueTagPrefix();
            }
        });
    }

    /**
     * Generate unique tag prefix from restaurant name
     */
    public function generateUniqueTagPrefix()
    {
        // Take first 3 letters of restaurant name, uppercase
        $name = preg_replace('/[^A-Za-z]/', '', $this->name);
        $basePrefix = strtoupper(substr($name, 0, 3));

        // If less than 3 chars, pad with X
        $basePrefix = str_pad($basePrefix, 3, 'X');

        // Check if exists, if so add number
        $prefix = $basePrefix;
        $counter = 1;

        while (Restaurant::where('tag_prefix', $prefix)->where('id', '!=', $this->id ?? 0)->exists()) {
            $prefix = substr($basePrefix, 0, 2).$counter;
            $counter++;
            if ($counter > 9) {
                // If all single digits used, use random 3 chars
                $prefix = $basePrefix.chr(rand(65, 90));
            }
        }

        return $prefix;
    }

    /**
     * Get next available table tag number
     */
    public function getNextTableTagNumber()
    {
        $lastTable = Table::withoutGlobalScopes()
            ->where('restaurant_id', $this->id)
            ->whereNotNull('table_tag')
            ->orderByRaw('CAST(SUBSTRING(table_tag, -2) AS UNSIGNED) DESC')
            ->first();

        if ($lastTable && preg_match('/(\d+)$/', $lastTable->table_tag, $matches)) {
            return (int) $matches[1] + 1;
        }

        return 1;
    }

    /**
     * Get next available waiter code number
     */
    public function getNextWaiterCodeNumber()
    {
        $lastWaiter = User::where('restaurant_id', $this->id)
            ->whereNotNull('waiter_code')
            ->orderByRaw('CAST(SUBSTRING(waiter_code, -2) AS UNSIGNED) DESC')
            ->first();

        if ($lastWaiter && preg_match('/(\d+)$/', $lastWaiter->waiter_code, $matches)) {
            return (int) $matches[1] + 1;
        }

        return 1;
    }

    /**
     * Generate table tag for this restaurant
     */
    public function generateTableTag($number = null)
    {
        $number = $number ?? $this->getNextTableTagNumber();

        return $this->tag_prefix.'-T'.str_pad($number, 2, '0', STR_PAD_LEFT);
    }

    /**
     * Generate waiter code for this restaurant
     */
    public function generateWaiterCode($number = null)
    {
        $number = $number ?? $this->getNextWaiterCodeNumber();

        return $this->tag_prefix.'-W'.str_pad($number, 2, '0', STR_PAD_LEFT);
    }

    /**
     * Menu image URL for fetch (storage: storage/app/public/menu or menu_images).
     */
    public function menuImageUrl(): ?string
    {
        if (! $this->menu_image) {
            return null;
        }

        return route('storage.serve', ['path' => $this->menu_image]);
    }

    /**
     * Phone number shown to customers for support (WhatsApp bot). Prefers support_phone, else phone.
     */
    public function getCustomerSupportPhone(): ?string
    {
        return $this->support_phone ?? $this->phone;
    }

    /**
     * Get Selcom credentials array
     */
    public function getSelcomCredentials()
    {
        return [
            'vendor_id' => $this->selcom_vendor_id,
            'api_key' => $this->selcom_api_key,
            'api_secret' => $this->selcom_api_secret,
            'is_live' => $this->selcom_is_live,
        ];
    }

    /**
     * Check if Selcom is configured
     */
    public function hasSelcomConfigured()
    {
        return ! empty($this->selcom_vendor_id)
            && ! empty($this->selcom_api_key)
            && ! empty($this->selcom_api_secret);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function waiters()
    {
        return $this->hasMany(User::class)->role('waiter');
    }

    public function tables()
    {
        return $this->hasMany(Table::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function menuItems()
    {
        return $this->hasMany(MenuItem::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function feedback()
    {
        return $this->hasMany(Feedback::class);
    }

    public function tips()
    {
        return $this->hasMany(Tip::class);
    }

    public function getWhatsappQrUrlAttribute()
    {
        $botNumber = \App\Models\Setting::get('whatsapp_bot_number', '255794321510');
        // Strip non-numeric characters
        $cleanNumber = preg_replace('/[^0-9]/', '', $botNumber);

        $message = 'START_'.$this->id;

        return 'https://wa.me/'.$cleanNumber.'?text='.urlencode($message);
    }
}
