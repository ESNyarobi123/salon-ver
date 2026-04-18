<?php

namespace App\Providers;

use App\Models\MenuItem;
use App\Models\OrderItem;
use App\Notifications\SalaryPaymentConfirmed;
use App\Observers\OrderItemObserver;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        OrderItem::observe(OrderItemObserver::class);

        View::composer('layouts.waiter', function ($view): void {
            if (Auth::check() && Auth::user()->hasRole('waiter')) {
                $view->with('unreadSalaryCount', Auth::user()->unreadNotifications()
                    ->where('type', SalaryPaymentConfirmed::class)
                    ->count());
            }
        });

        View::composer('layouts.manager', function ($view): void {
            if (! Auth::check() || ! Auth::user()->hasRole('manager') || ! Auth::user()->restaurant_id) {
                $view->with('managerLowStockCount', 0);

                return;
            }

            $count = MenuItem::query()
                ->where('stock_tracked', true)
                ->whereRaw('stock_quantity <= low_stock_threshold')
                ->count();

            $view->with('managerLowStockCount', $count);
        });
    }
}
