<?php

use App\Http\Controllers\CalculerController;
use Illuminate\Support\Facades\Route;



Route::get('/', [CalculerController::class, 'index'])->name('salarier');
Route::get('/bulletin/{id}',[CalculerController::class,'bulletin'])->name('bulletinP');
Route::get('/prime/index',[CalculerController::class,'prime'])->name('prime.index');
Route::get('/prime/edit/{prime_id}',[CalculerController::class,'edit'])->name('prime.edit');
Route::put('/prime/update/{prime_id}',[CalculerController::class,'update'])->name('prime.update');
Route::get('/prime/create',[CalculerController::class,'create'])->name('prime.create');
Route::post('/prime/store',[CalculerController::class,'store'])->name('prime.store');
Route::get('/prime/destroy/{prime_id}',[CalculerController::class,'destroy'])->name('prime.destroy');
Route::get('/salarier/search/',[CalculerController::class,'search'])->name('salarier.search');
Route::get('/salarier/create/',[CalculerController::class,'createSalarier'])->name('salarier.create');
Route::post('/salarier/store/',[CalculerController::class,'storeSalarier'])->name('salarier.store');
Route::get('/salarier/{id}',[CalculerController::class,'destroySalarier'])->name('salarier.destroy');
Route::get('/salaire/create/{id}',[CalculerController::class,'salaire'])->name('salaire.create');
Route::post('/salaire/store',[CalculerController::class,'storesalaire'])->name('salaire.store');
Route::post('/bulletin/search',[CalculerController::class,'searchBulletin'])->name('bulletin.search');

