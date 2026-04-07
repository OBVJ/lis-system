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
        Schema::table('patients', function (Blueprint $table) {
            $table->dropForeign(['assigned_doctor_id']);
            $table->dropForeign(['referring_doctor_id']);
            $table->dropColumn(['assigned_doctor_id', 'referring_doctor_id']);
            
            $table->string('treating_doctor')->nullable();
            $table->string('referring_doctor')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn(['treating_doctor', 'referring_doctor']);
            $table->foreignId('assigned_doctor_id')->nullable()->constrained('doctors')->nullOnDelete();
            $table->foreignId('referring_doctor_id')->nullable()->constrained('doctors')->nullOnDelete();
        });
    }
};
