<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRopTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rop', function (Blueprint $table) {
            $table->id();  // Primary Key
            $table->unsignedBigInteger('product_id');  // Foreign Key dari tabel produk
            $table->integer('lead_time');  // Waktu tunggu (Lead Time)
            $table->integer('daily_usage');  // Penggunaan harian (Daily Usage)
            $table->integer('safety_stock');  // Stok pengaman (Safety Stock)
            $table->integer('rop');  // Reorder Point (ROP)
            $table->timestamps();

            // Menambahkan foreign key untuk menghubungkan dengan tabel produk
            $table->foreign('product_id')->references('id_produk')->on('produk')->onDelete('cascade');
        });
    }
 
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rop');
    }
}
