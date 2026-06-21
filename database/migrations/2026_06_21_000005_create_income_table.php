<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('income', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->enum('income_type', ['fdr_interest', 'donation', 'extra_contribution', 'other'])->default('other');
            $table->string('source');
            $table->decimal('amount', 10, 2);
            $table->string('reference')->nullable();
            $table->string('attachment')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['active', 'voided'])->default('active');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('income');
    }
};
