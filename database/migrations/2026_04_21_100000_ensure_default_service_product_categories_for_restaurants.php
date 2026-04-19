<?php

use App\Models\Restaurant;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Restaurant::query()->chunkById(100, function ($restaurants): void {
            foreach ($restaurants as $restaurant) {
                $restaurant->ensureDefaultCatalogCategories();
            }
        });
    }

    public function down(): void
    {
        //
    }
};
