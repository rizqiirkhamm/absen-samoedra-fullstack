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
        
// database/migrations/xxxx_create_attendances_table.php
Schema::create('attendances', function (Blueprint $table) {
    $table->id();
    $table->string('nama');
    $table->string('foto');
    $table->string('foto_checkout')->nullable();
    $table->string('lokasi');
    $table->string('hadir_untuk');
    $table->date('tanggal');
    $table->time('check_in');
    $table->time('check_out')->nullable();
    $table->string('early_leave_reason')->nullable();
    $table->integer('work_hours')->default(0); // Jam kerja dalam menit
    $table->decimal('attendance_percentage', 5, 2)->default(0); // Persentase kehadiran per hari
    $table->boolean('auto_checkout')->default(false);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
