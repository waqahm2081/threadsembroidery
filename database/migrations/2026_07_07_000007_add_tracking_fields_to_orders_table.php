<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('estimated_delivery_at')->nullable()->after('status');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->string('category_slug')->nullable()->after('product_title');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('estimated_delivery_at');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('category_slug');
        });
    }
};
