<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_bulk_enquiries', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('full_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('company_name')->nullable();
            $table->string('product_title');
            $table->string('product_style_code')->nullable();
            $table->string('category_slug')->nullable();
            $table->string('product_slug')->nullable();
            $table->string('colour_name')->nullable();
            $table->string('colour_code')->nullable();
            $table->string('colour_hex', 20)->nullable();
            $table->json('size_breakdown')->nullable();
            $table->unsignedInteger('quantity')->default(0);
            $table->json('decoration_methods')->nullable();
            $table->boolean('logo_setup')->default(false);
            $table->decimal('estimated_unit_price', 10, 2)->nullable();
            $table->decimal('estimated_subtotal', 10, 2)->nullable();
            $table->text('message')->nullable();
            $table->string('status')->default('submitted');
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_bulk_enquiries');
    }
};
