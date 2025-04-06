<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peminjaman_dokumen', function (Blueprint $table) {
            $table->id();
            // Relasi ke tabel nasabah
            $table->foreignId('nasabah_id')->constrained('nasabahs')->cascadeOnDelete();

            $table->string('nama_dokumen');
            $table->date('tanggal_pinjam')->nullable();
            $table->date('tanggal_selesai_pinjam')->nullable();
            $table->enum('status', [
                'Menunggu Verifikasi',
                'Disetujui',
                'Dibatalkan',
                'Dikembalikan'
            ])->default('Menunggu Verifikasi');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjaman_dokumen');
    }
};
