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
//    Route::get('/create', [UserController::class, 'create']);  // Menampilkan halaman form tambah user
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

// Route::group(['prefix' => 'barang'], function () {
//     Route::get('/', [BarangController::class, 'index']);               
//     Route::post('/list', [BarangController::class, 'list']);            
//     Route::get('/create', [BarangController::class, 'create']);     
//     Route::post('/', [BarangController::class, 'store']);              
//     Route::get('/{id}', [BarangController::class, 'show']);            
//     Route::put('/{id}', [BarangController::class, 'update']);         
//     Route::delete('/{id}', [BarangController::class, 'destroy']);      
//     Route::get('/create_ajax', [BarangController::class, 'create_ajax']); 
//     Route::post('/ajax', [BarangController::class, 'store_ajax'])->name('barang.store_ajax');                  
//     Route::get('/{id}/edit_ajax', [BarangController::class, 'edit_ajax']);          
//     Route::put('/{id}/update_ajax', [BarangController::class, 'update_ajax']);      
//     Route::get('/{id}/delete_ajax', [BarangController::class, 'confirm_ajax']);     
//     Route::delete('/{id}/delete_ajax', [BarangController::class, 'delete_ajax']);   
//     Route::delete('/barang/delete_ajax/{id}', [BarangController::class, 'delete_ajax']);
// });

Route::get('barang', [BarangController::class, 'index'])->name('barang.index');
Route::post('barang/list', [BarangController::class, 'list'])->name('barang.list');

Route::get('barang/create', [BarangController::class, 'create'])->name('barang.create');
Route::post('barang/store', [BarangController::class, 'store'])->name('barang.store');

Route::get('barang/edit/{id}', [BarangController::class, 'edit'])->name('barang.edit');
Route::put('barang/update/{id}', [BarangController::class, 'update'])->name('barang.update');

Route::delete('barang/delete/{id}', [BarangController::class, 'destroy'])->name('barang.destroy');

Route::prefix('level')->group(function () {
    Route::get('/', [LevelController::class, 'index'])->name('level.index');
    Route::post('/list', [LevelController::class, 'list'])->name('level.list');
    Route::post('/store', [LevelController::class, 'store'])->name('level.store');
    Route::put('/update/{id}', [LevelController::class, 'update'])->name('level.update');
    Route::delete('/destroy/{id}', [LevelController::class, 'destroy'])->name('level.destroy');
});

Route::group(['prefix' => 'kategori'], function () {
    Route::get('/', [KategoriController::class, 'index']);
    Route::get('/list', [KategoriController::class, 'list']);
    Route::get('/create', [KategoriController::class, 'create']);
    Route::post('/', [KategoriController::class, 'store']);
    Route::get('/{id}', [KategoriController::class, 'show']);
    Route::get('/{id}/edit', [KategoriController::class, 'edit']);
    Route::put('/{id}', [KategoriController::class, 'update']);
    Route::delete('/{id}', [KategoriController::class, 'destroy']);
});



