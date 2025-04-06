<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeminjamanDokumen extends Model
{
    use HasFactory;

    protected $table = 'peminjaman_dokumen';

    protected $fillable = [
        'nasabah_id',
        'nama_dokumen',
        'tanggal_pinjam',
        'tanggal_selesai_pinjam',
        'status',
    ];

    public function nasabah()
    {
        return $this->belongsTo(Nasabah::class, 'nasabah_id');
    }
}
