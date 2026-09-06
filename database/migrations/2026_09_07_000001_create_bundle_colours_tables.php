<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bundle_colours', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('hex', 20)->default('#1c1c1c');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('bundle_garment_colour', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bundle_garment_id')->constrained('bundle_garments')->cascadeOnDelete();
            $table->foreignId('bundle_colour_id')->constrained('bundle_colours')->cascadeOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->unique(['bundle_garment_id', 'bundle_colour_id'], 'bundle_garment_colour_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bundle_garment_colour');
        Schema::dropIfExists('bundle_colours');
    }
};
