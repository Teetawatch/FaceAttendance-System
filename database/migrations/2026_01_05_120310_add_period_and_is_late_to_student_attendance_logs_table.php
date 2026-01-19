<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('student_attendance_logs', function (Blueprint $table) {
            $table->string('period')->nullable()->after('scan_type')->comment('morning or afternoon');
            $table->boolean('is_late')->default(false)->after('period');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_attendance_logs', function (Blueprint $table) {
            $table->dropColumn(['period', 'is_late']);
        });
    }
};
