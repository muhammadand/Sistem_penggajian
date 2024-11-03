<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\KeranjangController;
use App\Http\Controllers\RopController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\TransaksiPenjualanController;
use App\Http\Controllers\SafetyStockController;
//PRODUK
Route::resource('produk', ProdukController::class);
Route::get('produk/export', [ProdukController::class, 'export'])->name('produk.export');
Route::post('produk/import', [ProdukController::class, 'import'])->name('produk.import');
Route::get('/produk/{id_produk}/hitung-rata-rata', [ProdukController::class, 'hitungRataRata'])->name('produk.hitung-rata-rata');


//ADMIN 
Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
Route::get('/admin/rop', [AdminController::class, 'ROP'])->name('admin.rop');



//KERANJANG

Route::get('/keranjang', [KeranjangController::class, 'index'])->name('keranjang.index');
Route::post('/keranjang/cari', [KeranjangController::class, 'cari'])->name('keranjang.cari');
Route::post('keranjang/cari', [KeranjangController::class, 'cari'])->name('keranjang.cari');
Route::post('/keranjang/tambah', [KeranjangController::class, 'tambahProduk'])->name('keranjang.tambahProduk');
Route::post('/keranjang/store', [KeranjangController::class, 'store'])->name('keranjang.store');
Route::put('/keranjang/update/{id}', [KeranjangController::class, 'update'])->name('keranjang.update');
Route::delete('/keranjang/{id}', [KeranjangController::class, 'destroy'])->name('keranjang.destroy');


// REORDER POINT
Route::prefix('rop')->group(function () {
    Route::get('/', [RopController::class, 'index'])->name('rop.index'); // Menampilkan daftar ROP
    Route::get('/create', [RopController::class, 'create'])->name('rop.create'); // Menampilkan formulir untuk menambahkan ROP baru
    Route::post('/calculate', [RopController::class, 'calculate'])->name('rop.calculate'); // Menghitung dan menyimpan ROP
    Route::get('/{id}/edit', [RopController::class, 'edit'])->name('rop.edit'); // Menampilkan formulir untuk mengedit ROP
    Route::put('/{id}', [RopController::class, 'update'])->name('rop.update'); // Memperbarui ROP yang ada
    Route::delete('/{id}', [RopController::class, 'destroy'])->name('rop.destroy'); // Menghapus ROP yang ada
});
Route::post('/update-rop', [RopController::class, 'updateRop']);

Route::get('/reorder', [RopController::class, 'data'])->name('rop.data');


//KATEGORI
Route::resource('kategori', KategoriController::class);

//TRANSAKSI PENJUALAN
Route::post('/transaksi', [TransaksiPenjualanController::class, 'store'])->name('transaksi.store');
Route::get('/transaksi', [TransaksiPenjualanController::class, 'index'])->name('transaksi.index');
Route::get('/transaksi/search', [TransaksiPenjualanController::class, 'search'])->name('transaksi.search');
Route::get('transaksi/{id_transaksi}/edit', [TransaksiPenjualanController::class, 'edit'])->name('transaksi.edit');
Route::post('transaksi/{id_transaksi}', [TransaksiPenjualanController::class, 'update'])->name('transaksi.update');
Route::delete('transaksi/{id_transaksi}', [TransaksiPenjualanController::class, 'destroy'])->name('transaksi.destroy');
Route::get('/transaksi/laporan', [TransaksiPenjualanController::class, 'laporan'])->name('transaksi.laporan');


//SAFETY STOCK
Route::prefix('safety')->group(function () {
    Route::get('/', [SafetyStockController::class, 'index'])->name('safety.index');
    Route::get('/create', [SafetyStockController::class, 'create'])->name('safety.create');
    Route::post('/', [SafetyStockController::class, 'store'])->name('safety.store');
    Route::get('/{id}', [SafetyStockController::class, 'show'])->name('safety.show');
    Route::get('/{id}/edit', [SafetyStockController::class, 'edit'])->name('safety.edit');
    Route::put('/{id}', [SafetyStockController::class, 'update'])->name('safety.update');
    Route::delete('/{id}', [SafetyStockController::class, 'destroy'])->name('safety.destroy');
});




//USER 
Route::get('register', [UserController::class, 'register'])->name('register');
Route::post('register', [UserController::class, 'register_action'])->name('register.action');
Route::get('/', [UserController::class, 'login'])->name('login');
Route::post('login', [UserController::class, 'login_action'])->name('login.action');
Route::get('password', [UserController::class, 'password'])->name('password');
Route::post('password', [UserController::class, 'password_action'])->name('password.action');
Route::get('logout', [UserController::class, 'logout'])->name('logout');


























Route::get('/riwayat', [AdminController::class, 'riwayat'])->name('riwayat.index');




// Route untuk transaksi




















/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// home user



// home admin

























