<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->boolean('vat_enabled')->default(false)->after('pin');
            $table->decimal('vat_percent', 5, 2)->default(20)->after('vat_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn(['vat_enabled', 'vat_percent']);
        });
    }
};
