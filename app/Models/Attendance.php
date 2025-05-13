<?php

// app/Models/Attendance.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'nama',
        'foto',
        'foto_checkout',
        'lokasi',
        'hadir_untuk',
        'tanggal',
        'check_in',
        'check_out',
        'early_leave_reason',
        'work_hours',
        'attendance_percentage',
        'auto_checkout'
    ];
    
    protected $casts = [
        'tanggal' => 'date',
        'check_in' => 'datetime:H:i:s',
        'check_out' => 'datetime:H:i:s',
        'work_hours' => 'integer',
        'attendance_percentage' => 'decimal:2'
    ];
}