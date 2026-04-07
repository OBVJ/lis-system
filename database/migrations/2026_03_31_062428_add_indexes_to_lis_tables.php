<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->index('patient_code');
        });

        Schema::table('tests', function (Blueprint $table) {
            $table->index('category_id');
        });

        Schema::table('lab_requests', function (Blueprint $table) {
            $table->index('patient_id');
            $table->index('status');
        });

        Schema::table('lab_request_items', function (Blueprint $table) {
            $table->index('request_id');
            $table->index('test_id');
            $table->index('status');
        });

        Schema::table('samples', function (Blueprint $table) {
            $table->index('request_id');
            $table->string('barcode')->nullable()->after('status');
            $table->index('barcode');
        });

        Schema::table('test_results', function (Blueprint $table) {
            $table->index('request_item_id');
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) { $table->dropIndex(['patient_code']); });
        Schema::table('tests', function (Blueprint $table) { $table->dropIndex(['category_id']); });
        Schema::table('lab_requests', function (Blueprint $table) { $table->dropIndex(['patient_id', 'status']); });
        Schema::table('lab_request_items', function (Blueprint $table) { $table->dropIndex(['request_id', 'test_id', 'status']); });
        Schema::table('samples', function (Blueprint $table) { $table->dropColumn('barcode'); });
        Schema::table('test_results', function (Blueprint $table) { $table->dropIndex(['request_item_id']); });
    }
};
