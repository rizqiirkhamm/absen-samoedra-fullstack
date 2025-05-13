<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCheckoutFieldsToAttendancesTable extends Migration
{
    public function up()
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Tambahkan kolom hanya jika belum ada
            if (!Schema::hasColumn('attendances', 'foto_checkout')) {
                $table->string('foto_checkout')->nullable()->after('foto');
            }
            if (!Schema::hasColumn('attendances', 'early_leave_reason')) {
                $table->text('early_leave_reason')->nullable()->after('work_hours');
            }
        });
    }

    public function down()
    {
        Schema::table('attendances', function (Blueprint $table) {
            if (Schema::hasColumn('attendances', 'foto_checkout')) {
                $table->dropColumn('foto_checkout');
            }
            if (Schema::hasColumn('attendances', 'early_leave_reason')) {
                $table->dropColumn('early_leave_reason');
            }
        });
    }
}