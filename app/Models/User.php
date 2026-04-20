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
     * Normalize lookup input to match how `global_waiter_number` is stored.
     * Accepts 8-char hex (current), 1–4 digit numeric zero-padded (legacy), or `TIPTAP-W-#####`.
     */
    public static function normalizeGlobalWaiterNumberForLookup(string $raw): string
    {
        $raw = trim($raw);
        if (preg_match('/^TIPTAP-W-\d+$/i', $raw)) {
            return strtoupper($raw);
        }
        if (preg_match('/^\d{1,4}$/', $raw)) {
            return str_pad($raw, 4, '0', STR_PAD_LEFT);
        }

        return strtoupper($raw);
    }

    /**
     * Generate a unique global stylist id (8 uppercase hex characters).
     *
     * @param  list<string>  $alsoReserved  Treat these as taken (e.g. batch backfill dry-run).
     *
     * @throws \RuntimeException When a new unique value could not be generated after several attempts.
     */
    public static function generateGlobalWaiterNumber(array $alsoReserved = []): string
    {
        $taken = [];
        foreach ($alsoReserved as $code) {
            if (is_string($code) && $code !== '') {
                $taken[strtoupper(trim($code))] = true;
            }
        }

        foreach (self::query()->whereNotNull('global_waiter_number')->pluck('global_waiter_number') as $v) {
            $taken[strtoupper((string) $v)] = true;
        }

        for ($t = 0; $t < 64; $t++) {
            $candidate = strtoupper(bin2hex(random_bytes(4)));
            if (! isset($taken[$candidate])) {
                return $candidate;
            }
        }

        throw new \RuntimeException('Could not generate a unique global_waiter_number.');
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
