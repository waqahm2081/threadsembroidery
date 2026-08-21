<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_uniform_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('school_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status', 40)->default('pending');
            // contact
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone')->nullable();
            // child/pupil
            $table->string('pupil_name')->nullable();
            $table->string('pupil_class')->nullable();
            // shipping
            $table->string('shipping_address_line1')->nullable();
            $table->string('shipping_address_line2')->nullable();
            $table->string('shipping_city')->nullable();
            $table->string('shipping_postcode')->nullable();
            $table->string('shipping_country')->default('GB');
            // money
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('vat_amount', 10, 2)->default(0);
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('school_uniform_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_uniform_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('school_product_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('school_product_variant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name');
            $table->string('size', 40);
            $table->unsignedSmallInteger('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('line_total', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_uniform_order_items');
        Schema::dropIfExists('school_uniform_orders');
    }
};
