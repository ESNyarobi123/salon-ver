<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public const KIND_BOOKING = 'booking';

    public const KIND_PRODUCT_SALE = 'product_sale';

    /** Product retail sale awaiting USSD (no order_items yet — stock applied after payment). */
    public const STATUS_PAYMENT_PENDING = 'payment_pending';

    protected $fillable = [
        'restaurant_id',
        'order_kind',
        'waiter_id',
        'table_number',
        'customer_phone',
        'customer_name',
        'scheduled_at',
        'status',
        'payment_reference',
        'total_amount',
        'notes',
        'is_vip',
        'pending_line_items',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'is_vip' => 'boolean',
            'pending_line_items' => 'array',
        ];
    }

    protected static function booted()
    {
        static::addGlobalScope(new \App\Models\Scopes\RestaurantScope);
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function feedback()
    {
        return $this->hasOne(Feedback::class);
    }

    public function tip()
    {
        return $this->hasOne(Tip::class);
    }

    public function waiter()
    {
        return $this->belongsTo(User::class, 'waiter_id');
    }
}
