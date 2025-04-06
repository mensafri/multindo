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
        Schema::create('nasabahs', function (Blueprint $table) {
            $table->id();
            $table->string('noPin')->unique();
            $table->string('nama');
            $table->string('branch');
            $table->string('status')->default('Aktif'); // 'Aktif' atau 'Lunas'
            $table->string('gudang')->nullable();
            $table->string('rak_aplikasi')->nullable();
            $table->string('file')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nasabah');
    }
};
