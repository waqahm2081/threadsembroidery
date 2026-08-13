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
            $table->string('company_name')->nullable();
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('enquiry_type');
            $table->string('quantity')->nullable();
            $table->string('branding_method')->nullable();
            $table->json('placements')->nullable();
            $table->text('message');
            $table->string('status')->default('submitted');
            $table->text('admin_notes')->nullable();
            $table->decimal('quoted_amount', 10, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('branding_enquiry_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branding_enquiry_id')->constrained()->cascadeOnDelete();
            $table->string('original_name')->nullable();
            $table->string('file_path');
            $table->unsignedBigInteger('file_size')->nullable();
            $table->string('mime_type')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branding_enquiry_files');
        Schema::dropIfExists('branding_enquiries');
    }
};
