<?php

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login',[\App\Http\Controllers\AuthController::class,'login'])->name('login');
Route::post('/logout',[\App\Http\Controllers\AuthController::class,'logout'])->middleware('auth:sanctum');
Route::get('/tasks', [\App\Http\Controllers\TaskController::class, 'index'])->middleware('auth:sanctum');
Route::get('/task/{id}', [\App\Http\Controllers\TaskController::class, 'show'])->middleware('auth:sanctum');
Route::put('/task/{id}', [\App\Http\Controllers\TaskController::class, 'update'])->middleware('auth:sanctum');
Route::post('/task', [\App\Http\Controllers\TaskController::class, 'store'])->middleware('auth:sanctum');
Route::delete('/task/{id}', [\App\Http\Controllers\TaskController::class, 'delete'])->middleware('auth:sanctum');

Route::fallback(function () {
    return response()->json(['message' => 'Route not found'], 404);
});
