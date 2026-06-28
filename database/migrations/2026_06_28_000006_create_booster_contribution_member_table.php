<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booster_contribution_member', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booster_contribution_id')->constrained('booster_contributions')->cascadeOnDelete();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->decimal('expected_amount', 12, 2)->default(0);
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->unique(['booster_contribution_id', 'member_id'], 'booster_member_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booster_contribution_member');
    }
};
