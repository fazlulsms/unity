<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('membership_applications', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('photo')->nullable();
            $table->string('phone', 20);
            $table->string('email')->nullable();
            $table->text('address');
            $table->date('date_of_birth')->nullable();
            $table->string('profession')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->string('nominee_name')->nullable();
            $table->string('nominee_contact')->nullable();
            $table->boolean('is_existing_member')->default(false);
            $table->date('membership_date')->nullable();
            $table->decimal('monthly_fee_amount', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_remarks')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();

            $table->foreign('reviewed_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('membership_applications');
    }
};
