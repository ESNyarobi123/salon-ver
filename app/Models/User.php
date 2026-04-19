<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'restaurant_id',
        'waiter_code',
        'employment_type',
        'linked_until',
        'global_waiter_number',
        'phone',
        'location',
        'profile_photo_path',
        'is_online',
        'last_online_at',
    ];

    /**
     * Profile photo fetch URL. Served via route so it works on host even without storage:link.
     */
    public function profilePhotoUrl(): ?string
    {
        if (! $this->profile_photo_path) {
            return null;
        }

        $path = route('storage.serve', ['path' => $this->profile_photo_path], false);
        $ts = $this->updated_at?->timestamp ?? '';

        return $ts ? $path.'?v='.$ts : $path;
    }

    /**
     * Generate next global waiter number (e.g. TIPTAP-W-00001).
     */
    public static function generateGlobalWaiterNumber(): string
    {
        $last = self::whereNotNull('global_waiter_number')
            ->where('global_waiter_number', 'like', 'TIPTAP-W-%')
            ->orderByRaw('CAST(SUBSTRING(global_waiter_number, 10) AS UNSIGNED) DESC')
            ->value('global_waiter_number');

        $num = 1;
        if ($last && preg_match('/TIPTAP-W-(\d+)$/', $last, $m)) {
            $num = (int) $m[1] + 1;
        }

        return 'TIPTAP-W-'.str_pad((string) $num, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Whether this waiter's link to a restaurant is still active (not expired).
     * Temporary links expire at end of linked_until date.
     */
    public function isLinkActive(): bool
    {
        if (! $this->restaurant_id) {
            return false;
        }
        if ($this->employment_type !== 'temporary') {
            return true;
        }
        if (! $this->linked_until) {
            return true;
        }

        return Carbon::parse($this->linked_until)->endOfDay()->isFuture();
    }

    /**
     * Clear link when temporary contract has expired (history is preserved).
     */
    public function terminateExpiredLink(): void
    {
        if (! $this->restaurant_id || $this->isLinkActive()) {
            return;
        }
        $this->restaurant_id = null;
        $this->waiter_code = null;
        $this->employment_type = null;
        $this->linked_until = null;
        $this->save();
    }

    public function scopeActiveAtRestaurant($query, int $restaurantId)
    {
        return $query->where('restaurant_id', $restaurantId)
            ->where(function ($q) {
                $q->where('employment_type', '!=', 'temporary')
                    ->orWhereNull('employment_type')
                    ->orWhere('linked_until', '>=', Carbon::today()->toDateString());
            });
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'waiter_id');
    }

    public function tips()
    {
        return $this->hasMany(Tip::class, 'waiter_id');
    }

    public function feedback()
    {
        return $this->hasMany(Feedback::class, 'waiter_id');
    }

    public function waiterSalaryPayments()
    {
        return $this->hasMany(WaiterSalaryPayment::class);
    }

    /**
     * Get WhatsApp QR URL for this waiter
     */
    public function getWaiterQrUrlAttribute()
    {
        if (! $this->restaurant_id) {
            return null;
        }

        $botNumber = \App\Models\Setting::get('whatsapp_bot_number', '255794321510');
        $cleanNumber = preg_replace('/[^0-9]/', '', $botNumber);

        // WhatsApp prefill: S = stylist user id (avoids mixing with saloon tags PREFIX-W##).
        // Legacy START_{id}_W{user} is still accepted in parseEntry for old printed QRs.
        $message = 'START_'.$this->restaurant_id.'_S'.$this->id;

        return 'https://wa.me/'.$cleanNumber.'?text='.urlencode($message);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'linked_until' => 'date',
            'password' => 'hashed',
            'is_online' => 'boolean',
            'last_online_at' => 'datetime',
        ];
    }

    /**
     * Scope: waiters who are currently online (for restaurant).
     */
    public function scopeOnline($query)
    {
        return $query->where('is_online', true);
    }
}
