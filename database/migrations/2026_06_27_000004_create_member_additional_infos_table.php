<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_additional_infos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->unique()->constrained('members')->cascadeOnDelete();
            $table->text('present_address')->nullable();
            $table->text('permanent_address')->nullable();
            $table->text('business_address')->nullable();
            $table->string('primary_emergency_name')->nullable();
            $table->string('primary_emergency_relationship')->nullable();
            $table->string('primary_emergency_phone')->nullable();
            $table->string('secondary_emergency_name')->nullable();
            $table->string('secondary_emergency_relationship')->nullable();
            $table->string('secondary_emergency_phone')->nullable();
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])->nullable();
            $table->string('religion')->nullable();
            $table->date('marriage_anniversary')->nullable();
            $table->string('blood_group', 5)->nullable();
            $table->string('nationality')->nullable()->default('Bangladeshi');
            $table->string('nid_passport')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_additional_infos');
    }
};
