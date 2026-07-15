<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pembayaran')->unique();
            $table->unsignedBigInteger('penjualan_id');
            $table->date('tanggal_pembayaran');
            $table->decimal('nilai_bayar', 15, 2);
            $table->tinyInteger('deleted')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->foreign('penjualan_id')->references('id')->on('penjualans')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
    }
};
