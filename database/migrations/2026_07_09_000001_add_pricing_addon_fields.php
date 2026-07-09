<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->decimal('logo_setup_fee', 10, 2)->default(0)->after('decoration_fee');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('logo_setup_fee', 10, 2)->default(0)->after('decoration_fee');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('vat_amount', 10, 2)->default(0)->after('subtotal');
        });
    }

    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropColumn('logo_setup_fee');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('logo_setup_fee');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('vat_amount');
        });
    }
};
