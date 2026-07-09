<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branding_enquiries', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('full_name');
            $table->string('business_name');
            $table->string('email');
            $table->string('whatsapp');
            $table->text('garments');
            $table->string('colours');
            $table->json('placements');
            $table->string('branding_method');
            $table->boolean('min_order_confirmed')->default(false);
            $table->string('status')->default('submitted');
            $table->text('admin_notes')->nullable();
            $table->decimal('quoted_amount', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branding_enquiries');
    }
};
