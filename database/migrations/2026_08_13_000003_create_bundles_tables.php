<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bundles', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('icon')->default('bi-box');
            $table->string('tagline')->nullable();
            $table->string('badge')->nullable();
            $table->text('blurb')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('save_amount', 10, 2)->default(0);
            $table->json('methods');
            $table->boolean('featured')->default(false);
            $table->boolean('popular')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('bundle_includes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bundle_id')->constrained()->cascadeOnDelete();
            $table->string('text');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('bundle_garments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bundle_id')->constrained()->cascadeOnDelete();
            $table->string('category_slug');
            $table->unsignedInteger('qty');
            $table->string('label');
            $table->string('size_guide_key')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bundle_garments');
        Schema::dropIfExists('bundle_includes');
        Schema::dropIfExists('bundles');
    }
};
