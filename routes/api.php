<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/info', [AuthController::class, 'show']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/', [PostController::class,'index'] );
    Route::get('posts/{id}', [PostController::class ,'show']);
    Route::put('posts/{id}', [PostController::class ,'update']);
    Route::delete('posts/{id}', [PostController::class ,'destroy']);
    Route::post('posts/', [PostController::class ,'store']);

});
