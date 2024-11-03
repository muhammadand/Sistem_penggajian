<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSafetyStockTable extends Migration
{
    public function up()
    {
        Schema::create('safety_stock', function (Blueprint $table) {
            $table->id('id'); // Primary Key
            $table->unsignedBigInteger('id_produk'); // Foreign Key ke tabel produk
            $table->integer('permintaan_harian'); // Permintaan harian
            $table->integer('waktu_pengiriman'); // Waktu pengiriman dalam hari
            $table->integer('safety_stock'); // Safety stock yang dihitung
            $table->timestamps();

            // Foreign key ke tabel produk
            $table->foreign('id_produk')->references('id_produk')->on('produk')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('safety_stock');
    }
}
