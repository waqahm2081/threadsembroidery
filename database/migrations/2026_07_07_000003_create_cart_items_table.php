<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('product_id')->nullable();
            $table->string('style_code');
            $table->string('product_title');
            $table->string('category_slug');
            $table->string('product_slug');
            $table->string('colour_code')->nullable();
            $table->string('colour_name')->nullable();
            $table->string('colour_hex')->nullable();
            $table->string('size')->nullable();
            $table->json('size_breakdown')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->decimal('decoration_fee', 10, 2)->default(0);
            $table->boolean('is_customized')->default(false);
            $table->json('design_data')->nullable();
            $table->string('design_file_path')->nullable();
            $table->json('design_assets')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
