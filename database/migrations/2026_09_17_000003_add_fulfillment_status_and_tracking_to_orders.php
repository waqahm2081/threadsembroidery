<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['orders', 'shop_orders', 'school_uniform_orders'] as $table) {
            if (! Schema::hasColumn($table, 'tracking_number')) {
                Schema::table($table, function (Blueprint $blueprint) {
                    $blueprint->string('tracking_number')->nullable()->after('status');
                });
            }
        }

        $map = [
            'pending' => 'paid',
            'processing' => 'being_prepared',
            'shipped' => 'dispatched',
        ];

        foreach (['orders', 'shop_orders', 'school_uniform_orders'] as $table) {
            foreach ($map as $from => $to) {
                DB::table($table)->where('status', $from)->update(['status' => $to]);
            }
        }
    }

    public function down(): void
    {
        $map = [
            'paid' => 'pending',
            'being_prepared' => 'processing',
            'in_production' => 'processing',
            'ready_to_pack' => 'processing',
            'dispatched' => 'shipped',
        ];

        foreach (['orders', 'shop_orders', 'school_uniform_orders'] as $table) {
            foreach ($map as $from => $to) {
                DB::table($table)->where('status', $from)->update(['status' => $to]);
            }
        }

        foreach (['orders', 'shop_orders', 'school_uniform_orders'] as $table) {
            if (Schema::hasColumn($table, 'tracking_number')) {
                Schema::table($table, function (Blueprint $blueprint) {
                    $blueprint->dropColumn('tracking_number');
                });
            }
        }
    }
};
