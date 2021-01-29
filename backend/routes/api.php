<?php
 
use Illuminate\Support\Facades\Route;

// use Illuminate\Http\Request;
// use App\Http\Controllers\BookController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

//Route::get('/book', [BookController::class,'index']);   
//Route::get('/book', [App\Http\Controllers\BookController::class, 'index'])->name('book');

Route::middleware('auth:sanctum')->get('/book', [App\Http\Controllers\BookController::class, 'index'])->name('book');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) { return $request->user(); });