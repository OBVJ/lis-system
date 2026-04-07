<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('samples', function (Blueprint $table) {
            if (!Schema::hasColumn('samples', 'barcode')) {
                $table->string('barcode')->nullable()->after('status');
            }
            if (!Schema::hasColumn('samples', 'technician_name')) {
                $table->string('technician_name')->nullable()->after('barcode');
            }
        });
    }

    public function down(): void
    {
        Schema::table('samples', function (Blueprint $table) {
            $table->dropColumn(['barcode', 'technician_name']);
        });
    }
};
