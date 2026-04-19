<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['restaurant_id', 'waiter_id', 'table_number', 'customer_phone', 'customer_name', 'scheduled_at', 'status', 'payment_reference', 'total_amount', 'notes', 'is_vip'];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'is_vip' => 'boolean',
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
