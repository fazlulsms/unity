<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE membership_applications MODIFY COLUMN status ENUM('pending','under_review','more_info_required','photo_required','approved','rejected') NOT NULL DEFAULT 'pending'");

        Schema::table('membership_applications', function (Blueprint $table) {
            $table->text('internal_notes')->nullable()->after('review_remarks');
        });
    }

    public function down(): void
    {
        Schema::table('membership_applications', function (Blueprint $table) {
            $table->dropColumn('internal_notes');
        });

        DB::statement("ALTER TABLE membership_applications MODIFY COLUMN status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending'");
    }
};
