<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fdr_records', function (Blueprint $table) {
            $table->foreignId('bank_account_id')->nullable()->after('id')
                ->constrained('bank_accounts')->nullOnDelete();
            $table->decimal('principal_returned', 12, 2)->nullable()->after('actual_maturity_amount');
            $table->decimal('tax_deduction', 12, 2)->default(0)->after('interest_received');
        });
    }

    public function down(): void
    {
        Schema::table('fdr_records', function (Blueprint $table) {
            $table->dropForeign(['bank_account_id']);
            $table->dropColumn(['bank_account_id', 'principal_returned', 'tax_deduction']);
        });
    }
};
