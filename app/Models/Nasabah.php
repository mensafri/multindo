<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nasabah extends Model
{
    use HasFactory;

    protected $table = 'nasabahs';

    protected $fillable = [
        'noPin',
        'nama',
        'branch',
        'status',
        'gudang',
        'rak_aplikasi',
        'file',
    ];
}
