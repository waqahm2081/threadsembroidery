<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shop_product_bulk_discounts', function (Blueprint $table) {
            $table->id();
            $table->string('style_code')->index();
            $table->unsignedInteger('qty_from');
            $table->unsignedInteger('qty_to');
            $table->decimal('discount_percent', 5, 2);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['style_code', 'qty_from', 'qty_to'], 'shop_bulk_discount_range_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_product_bulk_discounts');
    }
};
