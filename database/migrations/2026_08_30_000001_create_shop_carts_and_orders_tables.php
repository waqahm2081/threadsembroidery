<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shop_carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_id')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('shop_cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_cart_id')->constrained()->cascadeOnDelete();
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
            $table->decimal('logo_setup_fee', 10, 2)->default(0);
            $table->boolean('is_customized')->default(false);
            $table->json('design_data')->nullable();
            $table->string('design_file_path')->nullable();
            $table->json('design_assets')->nullable();
            $table->timestamps();
        });

        Schema::create('shop_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('pending');
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->string('company_name')->nullable();
            $table->string('shipping_address_line1');
            $table->string('shipping_city');
            $table->string('shipping_state');
            $table->string('shipping_zip');
            $table->boolean('billing_same_as_shipping')->default(true);
            $table->string('billing_address_line1')->nullable();
            $table->string('billing_city')->nullable();
            $table->string('billing_state')->nullable();
            $table->string('billing_zip')->nullable();
            $table->string('billing_company_name')->nullable();
            $table->text('additional_instructions')->nullable();
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('vat_amount', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('shop_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_order_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('product_id')->nullable();
            $table->string('style_code');
            $table->string('product_title');
            $table->string('category_slug')->nullable();
            $table->string('colour_name')->nullable();
            $table->string('colour_code')->nullable();
            $table->string('size')->nullable();
            $table->json('size_breakdown')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->decimal('decoration_fee', 10, 2)->default(0);
            $table->decimal('logo_setup_fee', 10, 2)->default(0);
            $table->decimal('line_total', 10, 2)->default(0);
            $table->json('design_data')->nullable();
            $table->string('design_file_path')->nullable();
            $table->json('design_assets')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_order_items');
        Schema::dropIfExists('shop_orders');
        Schema::dropIfExists('shop_cart_items');
        Schema::dropIfExists('shop_carts');
    }
};
