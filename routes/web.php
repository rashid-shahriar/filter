<?php

use App\Http\Controllers\NoticeController;
use Illuminate\Support\Facades\Route;
use Mockery\Matcher\Not;

// Route::get('/', function () {
//     return view('welcome');
// });


Route::get('/', [NoticeController::class, 'index']);
Route::post('/filter-notices', [NoticeController::class, 'filter'])->name('notices.filter');
