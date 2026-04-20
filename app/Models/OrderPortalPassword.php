<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Hash;

class OrderPortalPassword extends Model
{
    protected $fillable = [
        'restaurant_id',
        'user_id',
        'password',
        'generated_at',
        'revoked_at',
    ];

    protected function casts(): array
    {
        return [
            'generated_at' => 'datetime',
            'revoked_at' => 'datetime',
        ];
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isActive(): bool
    {
        return $this->revoked_at === null;
    }

    public function setPasswordAttribute(string $value): void
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function checkPassword(string $plain): bool
    {
        return Hash::check($plain, $this->password);
    }

    /**
     * Generate a unique 4-digit numeric PIN (0000–9999) for one-time display.
     * Plain PINs must not collide across non-revoked rows: login scans all active credentials with Hash::check.
     */
    public static function generateRandomPassword(): string
    {
        $active = self::query()->whereNull('revoked_at')->get();

        $isTaken = function (string $plain) use ($active): bool {
            foreach ($active as $credential) {
                if ($credential->checkPassword($plain)) {
                    return true;
                }
            }

            return false;
        };

        for ($t = 0; $t < 64; $t++) {
            $plain = str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
            if (! $isTaken($plain)) {
                return $plain;
            }
        }

        for ($i = 0; $i <= 9999; $i++) {
            $plain = str_pad((string) $i, 4, '0', STR_PAD_LEFT);
            if (! $isTaken($plain)) {
                return $plain;
            }
        }

        throw new \RuntimeException('No available 4-digit order portal PIN (0000–9999).');
    }
}
