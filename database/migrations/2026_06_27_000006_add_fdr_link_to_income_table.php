<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('income', function (Blueprint $table) {
            $table->foreignId('fdr_id')->nullable()->constrained('fdr_records')->nullOnDelete()->after('id');
            $table->string('source_module')->default('manual')->after('fdr_id'); // manual | fdr
        });
    }

    public function down(): void
    {
        Schema::table('income', function (Blueprint $table) {
            $table->dropForeign(['fdr_id']);
            $table->dropColumn(['fdr_id', 'source_module']);
        });
    }
};
