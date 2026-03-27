<?php

use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| YouTube Course Scraper Routes
|
*/

// Main page — category input form
Route::get('/', [CategoryController::class, 'index'])->name('home');

// Fetch playlists — processes categories via AI + YouTube
Route::post('/fetch', [CategoryController::class, 'fetch'])->name('fetch');

// Results page — paginated, filterable playlist cards
Route::get('/results', [CategoryController::class, 'results'])->name('results');
