<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->boolean('stock_tracked')->default(false)->after('is_available');
            $table->unsignedInteger('stock_quantity')->default(0)->after('stock_tracked');
            $table->unsignedInteger('low_stock_threshold')->default(5)->after('stock_quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropColumn(['stock_tracked', 'stock_quantity', 'low_stock_threshold']);
        });
    }
};
