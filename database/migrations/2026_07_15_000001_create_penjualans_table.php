<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penjualans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_penjualan')->unique();
            $table->date('tanggal_penjualan');
            $table->decimal('total', 15, 2);
            $table->enum('status', ['Belum Dibayar', 'Sudah Dibayar', 'Belum Dibayar Sepenuhnya'])->default('Belum Dibayar');
            $table->tinyInteger('deleted')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('deleted_by')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penjualans');
    }
};
