<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_quotes', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_slug')->nullable();
            $table->string('product_title');
            $table->string('product_brand')->nullable();
            $table->string('product_style_code')->nullable();
            $table->string('product_image')->nullable();
            $table->string('colour_name')->nullable();
            $table->string('colour_hex', 20)->nullable();
            $table->string('method', 40)->default('embroidery');
            $table->string('view', 20)->default('front');
            $table->json('placements')->nullable();
            $table->json('texts')->nullable();
            $table->json('size_breakdown')->nullable();
            $table->unsignedInteger('quantity')->default(0);
            $table->decimal('garment_unit', 10, 2)->default(0);
            $table->decimal('branding_unit', 10, 2)->default(0);
            $table->decimal('text_unit', 10, 2)->default(0);
            $table->decimal('setup_fee', 10, 2)->default(0);
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->string('preview_path')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('logo_name')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_quotes');
    }
};
