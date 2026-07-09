<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branding_enquiry_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branding_enquiry_id')->constrained()->cascadeOnDelete();
            $table->string('original_name');
            $table->string('file_path');
            $table->unsignedInteger('file_size');
            $table->string('mime_type')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branding_enquiry_files');
    }
};
