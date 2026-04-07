<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'paid_amount')) {
                $table->decimal('paid_amount', 15, 2)->default(0)->after('amount');
            }
            if (!Schema::hasColumn('payments', 'discount_type')) {
                $table->string('discount_type')->nullable()->after('status');
            }
            if (!Schema::hasColumn('payments', 'discount_value')) {
                $table->decimal('discount_value', 15, 2)->default(0)->after('discount_type');
            }
            if (!Schema::hasColumn('payments', 'refund_amount')) {
                $table->decimal('refund_amount', 15, 2)->default(0)->after('paid_amount');
            }
            if (!Schema::hasColumn('payments', 'refunded_at')) {
                $table->timestamp('refunded_at')->nullable()->after('refund_amount');
            }
            if (!Schema::hasColumn('payments', 'refund_note')) {
                $table->text('refund_note')->nullable()->after('refunded_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['paid_amount', 'discount_type', 'discount_value', 'refund_amount', 'refunded_at', 'refund_note']);
        });
    }
};
