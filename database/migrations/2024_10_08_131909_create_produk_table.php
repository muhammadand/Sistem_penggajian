<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProdukTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('produk', function (Blueprint $table) {
            $table->id('id_produk'); // Primary Key
            $table->string('nama_obat');
            $table->string('kode_obat')->unique();
            $table->unsignedBigInteger('kategori_obat'); // Foreign Key
            $table->integer('stok_awal');
            $table->integer('stok_sisa');
            $table->decimal('harga_beli', 15, 2); // Menambahkan harga beli
            $table->decimal('harga_jual', 15, 2);
            $table->string('satuan'); // Menambahkan satuan
            $table->decimal('total', 15, 2); // Menambahkan total
            $table->date('tanggal_kadaluarsa');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Foreign key to kategori table
            $table->foreign('kategori_obat')->references('id_kategori')->on('kategori')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('produk');
    }
}
