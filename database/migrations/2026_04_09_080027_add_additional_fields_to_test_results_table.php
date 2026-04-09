<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('test_results', function (Blueprint $table) {
            $table->string('reference_range')->nullable()->after('flag');
            $table->string('units')->nullable()->after('reference_range');
            $table->string('status')->default('completed')->after('units');
            $table->foreignId('entered_by')->nullable()->constrained('users')->onDelete('set null')->after('status');
            $table->timestamp('entered_at')->nullable()->after('entered_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('test_results', function (Blueprint $table) {
            $table->dropForeign(['entered_by']);
            $table->dropColumn(['reference_range', 'units', 'status', 'entered_by', 'entered_at']);
        });
    }
};
