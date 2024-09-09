<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('sanpham/thung-rac', [App\Http\Controllers\SanPhamController::class, 'danhSachSanPhamDaXoa'])->name('sanpham.thungrac');
Route::post('sanpham/thung-rac/{id}', [App\Http\Controllers\SanPhamController::class, 'khoiPhucSanPham'])->name('sanpham.khoiphuc');
Route::resource('sanpham', App\Http\Controllers\SanPhamController::class);
