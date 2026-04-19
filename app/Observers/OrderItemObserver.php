<?php

namespace App\Observers;

use App\Models\MenuItem;
use App\Models\OrderItem;
use Illuminate\Validation\ValidationException;

class OrderItemObserver
{
    public function creating(OrderItem $orderItem): void
    {
        if (! $orderItem->menu_item_id) {
            return;
        }

        $menuItem = MenuItem::withoutGlobalScopes()
            ->whereKey($orderItem->menu_item_id)
            ->with('category')
            ->lockForUpdate()
            ->first();

        if (! $menuItem || ! $menuItem->stock_tracked || ! $menuItem->category?->isProductCatalog()) {
            return;
        }

        if ($menuItem->stock_quantity < $orderItem->quantity) {
            throw ValidationException::withMessages([
                'stock' => "Not enough stock for {$menuItem->name}. Available: {$menuItem->stock_quantity}.",
            ]);
        }
    }

    public function created(OrderItem $orderItem): void
    {
        if (! $orderItem->menu_item_id) {
            return;
        }

        $menuItem = MenuItem::withoutGlobalScopes()
            ->with('category')
            ->find($orderItem->menu_item_id);
        if (! $menuItem || ! $menuItem->stock_tracked || ! $menuItem->category?->isProductCatalog()) {
            return;
        }

        MenuItem::withoutGlobalScopes()
            ->whereKey($menuItem->id)
            ->decrement('stock_quantity', $orderItem->quantity);
    }

    public function deleted(OrderItem $orderItem): void
    {
        if (! $orderItem->menu_item_id) {
            return;
        }

        $menuItem = MenuItem::withoutGlobalScopes()
            ->with('category')
            ->find($orderItem->menu_item_id);
        if (! $menuItem || ! $menuItem->stock_tracked || ! $menuItem->category?->isProductCatalog()) {
            return;
        }

        MenuItem::withoutGlobalScopes()
            ->whereKey($menuItem->id)
            ->increment('stock_quantity', $orderItem->quantity);
    }
}
