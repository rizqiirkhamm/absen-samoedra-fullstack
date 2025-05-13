<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'hadir_untuk'
    ];

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
