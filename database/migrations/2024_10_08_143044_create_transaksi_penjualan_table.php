<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransaksiPenjualanTable extends Migration
{
    public function up()
    {Schema::create('transaksi_penjualan', function (Blueprint $table) {
        $table->id('id_transaksi'); // Primary key
        $table->text('nama_obat')->nullable();
        $table->decimal('uang_masuk', 10, 2); // Uang yang diterima
        $table->integer('jumlah'); // Jumlah produk yang dibeli
        $table->unsignedBigInteger('produk_id'); // Relasi ke tabel produk
        $table->dateTime('tanggal_transaksi')->default(now()); // Tanggal transaksi
        $table->timestamps();
    
        // Definisi foreign key untuk relasi ke tabel produk
        $table->foreign('produk_id')->references('id_produk')->on('produk')->onDelete('cascade'); // Pastikan tabel dan kolom ini benar
    });
    }

    public function down()
    {
        Schema::table('transaksi_penjualan', function (Blueprint $table) {
            $table->dropColumn('nama_obat'); // Menghapus kolom saat rollback
        });
    }
}
