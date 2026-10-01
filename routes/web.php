<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\ShopController as Shop;
use App\Http\Controllers\AdminController as Admin;
Route::get('/',[Shop::class,'index']);
Route::get('/carrinho',[Shop::class,'cart']);
Route::post('/carrinho/{id}',[Shop::class,'add'])->whereNumber('id');
Route::patch('/carrinho/{id}',[Shop::class,'update'])->whereNumber('id');
Route::post('/finalizar',[Shop::class,'checkout'])->middleware('throttle:10,1');
Route::get('/pedido/{token}',[Shop::class,'order'])->whereUuid('token');
Route::view('/admin/login','shop.login')->name('login');
Route::post('/admin/login',[Admin::class,'login'])->middleware('throttle:5,1');
Route::middleware(['auth',\App\Http\Middleware\RequireAdmin::class])->prefix('admin')->group(function(){
 Route::get('/',[Admin::class,'dashboard']);
 Route::post('/sair',[Admin::class,'logout']);
 Route::patch('/pedidos/{id}',[Admin::class,'status'])->whereNumber('id');
 Route::get('/produtos',[Admin::class,'products']);
 Route::post('/produtos',[Admin::class,'save']);
 Route::put('/produtos/{id}',[Admin::class,'save'])->whereNumber('id');
});
