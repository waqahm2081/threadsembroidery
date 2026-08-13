<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_id')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained()->cascadeOnDelete();
            $table->string('item_type')->default('bundle');
            $table->foreignId('bundle_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('image_path')->nullable();
            $table->string('colour_name')->nullable();
            $table->string('colour_hex')->nullable();
            $table->string('method')->nullable();
            $table->json('size_breakdown')->nullable();
            $table->json('garments_snapshot')->nullable();
            $table->json('addons')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('base_price', 10, 2)->default(0);
            $table->decimal('addon_total', 10, 2)->default(0);
            $table->decimal('line_total', 10, 2)->default(0);
            $table->string('summary')->nullable();
            $table->timestamps();
        });

        Schema::create('cart_item_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_item_id')->constrained()->cascadeOnDelete();
            $table->string('placement_slug');
            $table->string('placement_label');
            $table->string('path');
            $table->string('original_name')->nullable();
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('pending');
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('company_name')->nullable();
            $table->string('shipping_address_line1');
            $table->string('shipping_address_line2')->nullable();
            $table->string('shipping_city');
            $table->string('shipping_postcode');
            $table->string('shipping_country')->default('United Kingdom');
            $table->text('notes')->nullable();
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('vat_amount', 10, 2)->default(0);
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('item_type')->default('bundle');
            $table->foreignId('bundle_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('image_path')->nullable();
            $table->string('colour_name')->nullable();
            $table->string('colour_hex')->nullable();
            $table->string('method')->nullable();
            $table->json('size_breakdown')->nullable();
            $table->json('garments_snapshot')->nullable();
            $table->json('addons')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('base_price', 10, 2)->default(0);
            $table->decimal('addon_total', 10, 2)->default(0);
            $table->decimal('line_total', 10, 2)->default(0);
            $table->string('summary')->nullable();
            $table->timestamps();
        });

        Schema::create('order_item_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')->constrained()->cascadeOnDelete();
            $table->string('placement_slug');
            $table->string('placement_label');
            $table->string('path');
            $table->string('original_name')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_item_files');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('cart_item_files');
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('carts');
    }
};
