<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fdr_records', function (Blueprint $table) {
            $table->date('closure_date')->nullable()->after('maturity_date');
            $table->decimal('actual_maturity_amount', 12, 2)->nullable()->after('expected_maturity_amount');
            $table->string('closure_attachment')->nullable()->after('attachment');
        });
    }

    public function down(): void
    {
        Schema::table('fdr_records', function (Blueprint $table) {
            $table->dropColumn(['closure_date', 'actual_maturity_amount', 'closure_attachment']);
        });
    }
};
