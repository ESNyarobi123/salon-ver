<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    public const CATALOG_KIND_SERVICE = 'service';

    public const CATALOG_KIND_PRODUCT = 'product';

    protected $fillable = ['restaurant_id', 'name', 'image', 'sort_order', 'catalog_kind'];

    protected static function booted()
    {
        static::addGlobalScope(new \App\Models\Scopes\RestaurantScope);
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function menuItems()
    {
        return $this->hasMany(MenuItem::class);
    }

    public function isProductCatalog(): bool
    {
        return $this->catalog_kind === self::CATALOG_KIND_PRODUCT;
    }

    /** @param  Builder<static>  $query */
    public function scopeProductCatalog(Builder $query): Builder
    {
        return $query->where('catalog_kind', self::CATALOG_KIND_PRODUCT);
    }

    /**
     * Image URL for display (works with or without storage:link via storage.serve).
     */
    public function imageUrl(): ?string
    {
        if (! $this->image) {
            return null;
        }

        return route('storage.serve', ['path' => $this->image], false);
    }
}
