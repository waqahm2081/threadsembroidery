<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pc_products', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique();
            $table->string('style_code')->index();
            $table->string('title');
            $table->text('body')->nullable();
            $table->string('brand')->nullable()->index();
            $table->string('gender')->nullable();
            $table->string('type')->nullable();
            $table->text('categories')->nullable();
            $table->text('collections')->nullable();
            $table->text('attributes')->nullable();
            $table->string('colourway_code')->nullable()->index();
            $table->string('colourway_name')->nullable();
            $table->string('colour_group')->nullable();
            $table->string('material')->nullable();
            $table->string('weight')->nullable();
            $table->string('rgb')->nullable();
            $table->text('image_model')->nullable();
            $table->text('image_front')->nullable();
            $table->text('image_back')->nullable();
            $table->text('image_side')->nullable();
            $table->text('image_detail')->nullable();
            $table->string('size')->nullable();
            $table->text('size_conversions')->nullable();
            $table->decimal('price_single', 10, 2)->default(0);
            $table->decimal('price_pack', 10, 2)->default(0);
            $table->unsignedInteger('pack_qty')->default(0);
            $table->decimal('price_carton', 10, 2)->default(0);
            $table->unsignedInteger('carton_qty')->default(0);
            $table->unsignedTinyInteger('vat_rate')->default(20);
            $table->unsignedInteger('grams')->default(0);
            $table->unsignedInteger('stock_fts')->default(0);
            $table->boolean('is_clearance')->default(false);
            $table->boolean('is_discontinued')->default(false);
            $table->boolean('is_new')->default(false);
            $table->boolean('is_special_order')->default(false);
            $table->timestamps();

            $table->index(['is_discontinued', 'style_code']);
        });

        Schema::create('pc_import_log', function (Blueprint $table) {
            $table->id();
            $table->string('type', 50);
            $table->string('status', 50);
            $table->unsignedInteger('rows')->default(0);
            $table->text('message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pc_import_log');
        Schema::dropIfExists('pc_products');
    }
};
