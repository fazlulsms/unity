<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fdr_records', function (Blueprint $table) {
            $table->id();
            $table->string('bank_name');
            $table->string('branch')->nullable();
            $table->string('fdr_number');
            $table->date('opening_date');
            $table->date('maturity_date');
            $table->decimal('principal_amount', 12, 2);
            $table->decimal('interest_rate', 5, 2);
            $table->decimal('expected_maturity_amount', 12, 2)->nullable();
            $table->decimal('interest_received', 12, 2)->default(0);
            $table->enum('status', ['active', 'matured', 'renewed', 'closed'])->default('active');
            $table->boolean('is_public_reference')->default(false);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fdr_records');
    }
};
