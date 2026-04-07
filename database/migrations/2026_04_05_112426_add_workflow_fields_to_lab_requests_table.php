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
        Schema::table('lab_requests', function (Blueprint $table) {
            $table->timestamp('collected_at')->nullable();
            $table->foreignId('collected_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamp('in_progress_at')->nullable();
            $table->foreignId('in_progress_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamp('review_at')->nullable();
            $table->foreignId('review_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('completed_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamp('delivered_at')->nullable();
            $table->foreignId('delivered_by')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lab_requests', function (Blueprint $table) {
            $table->dropForeign(['collected_by']);
            $table->dropForeign(['in_progress_by']);
            $table->dropForeign(['review_by']);
            $table->dropForeign(['completed_by']);
            $table->dropForeign(['delivered_by']);
            $table->dropColumn([
                'collected_at', 'collected_by',
                'in_progress_at', 'in_progress_by',
                'review_at', 'review_by',
                'completed_at', 'completed_by',
                'delivered_at', 'delivered_by'
            ]);
        });
    }
};
