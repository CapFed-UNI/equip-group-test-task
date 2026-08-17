<?php

use App\Http\Controllers\CatalogController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/group/{group}', [CatalogController::class, 'group'])->name('catalog.group');
Route::get('/product/{product}', [CatalogController::class, 'product'])->name('catalog.product');
