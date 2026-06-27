<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            // One-time admin-set opening balance / backdated contribution adjustment
            $table->decimal('joining_contribution', 10, 2)->default(0)->after('monthly_fee_amount');
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn('joining_contribution');
        });
    }
};
