<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('catalog_kind', 20)->default('service')->after('sort_order');
        });

        DB::table('categories')
            ->whereRaw('LOWER(TRIM(name)) IN (?, ?, ?)', ['product', 'products', 'retail'])
            ->update(['catalog_kind' => 'product']);

        DB::table('menu_items')
            ->join('categories', 'categories.id', '=', 'menu_items.category_id')
            ->where('categories.catalog_kind', 'product')
            ->update(['menu_items.stock_tracked' => 1]);

        DB::table('menu_items')
            ->join('categories', 'categories.id', '=', 'menu_items.category_id')
            ->where('categories.catalog_kind', 'service')
            ->update(['menu_items.stock_tracked' => 0]);
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('catalog_kind');
        });
    }
};
