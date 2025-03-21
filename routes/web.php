<?php

use App\Http\Controllers\KategoriController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\BarangController;
use Illuminate\Support\Facades\Route;

Route::get('/', [WelcomeController::class, 'index']);

Route::group(['prefix' => 'user'], function () {
    Route::get('/', [UserController::class, 'index']);         // Menampilkan halaman awal user
    Route::get('/list', [UserController::class, 'list']);      // Menampilkan data user dalam bentuk JSON untuk DataTables
    Route::post('/', [UserController::class, 'store']);        // Menyimpan data user baru
    Route::get('/create_ajax', [UserController::class, 'create_ajax']);      // Menampilkan halaman form tambah user Ajax
    Route::post('/ajax', [UserController::class, 'store_ajax']); // Menyimpan data user baru Ajax
    Route::get('/{id}', [UserController::class, 'show']);      // Menampilkan detail user
    Route::put('/{id}', [UserController::class, 'update']);    // Menyimpan perubahan data user
    Route::get('/{id}/edit_ajax', [UserController::class, 'edit_ajax']); // Menampilkan halaman form edit user Ajax
    Route::put('/{id}/update_ajax', [UserController::class, 'update_ajax']); // Menyimpan perubahan data user Ajax
    Route::get('/{id}/delete_ajax', [UserController::class, 'confirm_ajax']); // Untuk tampilkan form confirm delete user Ajax
    Route::delete('/{id}/delete_ajax', [UserController::class, 'delete_ajax']); // Untuk hapus data user Ajax
    Route::delete('/{id}', [UserController::class, 'destroy']); // Menghapus data user
});
// Barang CRUD routes (non-ajax)
Route::get('barang', [BarangController::class, 'index'])->name('barang.index');
Route::post('barang/list', [BarangController::class, 'list'])->name('barang.list');
Route::get('barang/create', [BarangController::class, 'create'])->name('barang.create');
Route::post('barang/store', [BarangController::class, 'store'])->name('barang.store');
Route::get('barang/edit/{id}', [BarangController::class, 'edit'])->name('barang.edit');
Route::put('barang/update/{id}', [BarangController::class, 'update'])->name('barang.update');
Route::delete('barang/delete/{id}', [BarangController::class, 'destroy'])->name('barang.destroy');

Route::get('/barang/create_ajax', [BarangController::class, 'create_ajax']);     
Route::post('barang/ajax/store', [BarangController::class, 'store_ajax'])->name('barang.ajax.store');
Route::get('barang/ajax/edit/{id}', [BarangController::class, 'edit_ajax'])->name('barang.ajax.edit');
Route::get('barang/ajax/confirm/{id}', [BarangController::class, 'confirm_ajax'])->name('barang.ajax.confirm');
Route::delete('barang/ajax/delete/{id}', [BarangController::class, 'delete_ajax'])->name('barang.ajax.delete');


// Halaman utama kategori
Route::get('/kategori', [KategoriController::class, 'index'])->name('kategori.index');
Route::post('/kategori/list', [KategoriController::class, 'list'])->name('kategori.list');
Route::get('/kategori/create_ajax', [KategoriController::class, 'create_ajax'])->name('kategori.create_ajax');
Route::get('/kategori/edit_ajax/{id}', [KategoriController::class, 'edit_ajax'])->name('kategori.ajax.edit');
Route::post('/kategori/store_ajax', [KategoriController::class, 'store_ajax'])->name('kategori.ajax.store');
Route::put('/kategori/update_ajax/{id}', [KategoriController::class, 'update_ajax'])->name('kategori.ajax.update');
Route::delete('/kategori/delete/{id}', [KategoriController::class, 'delete_ajax'])->name('kategori.ajax.delete');


Route::prefix('level')->name('level.')->group(function () {
    Route::get('/', [LevelController::class, 'index'])->name('index');
    Route::post('/list', [LevelController::class, 'list'])->name('list');
    Route::get('/create_ajax', [LevelController::class, 'create_ajax'])->name('create_ajax');
    Route::post('/store_ajax', [LevelController::class, 'store_ajax'])->name('store_ajax');
    Route::get('/{id}/confirm_ajax', [LevelController::class, 'confirm_ajax'])->name('confirm_ajax');
    Route::post('/ajax', [LevelController::class, 'store_ajax'])->name('store_ajax');
    Route::delete('/{id}/delete_ajax', [LevelController::class, 'delete_ajax'])->name('delete_ajax');
});





