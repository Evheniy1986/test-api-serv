<?php

use App\Http\Controllers\Api\V1\PositionController;
use App\Http\Controllers\Api\V1\TokkenController;
use App\Http\Controllers\Api\V1\UserController;
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
Route::prefix('v1')->group(function () {
    Route::get('positions', [PositionController::class, 'index']);
    Route::get('users', [UserController::class, 'index']);
    Route::get('edit', [UserController::class, 'edit'])->name('edit');
    Route::post('register', [UserController::class, 'registerUser'])->name('register');
    Route::get('users/{id}', [UserController::class, 'show']);
    Route::get('token', [TokkenController::class, 'generate']);
});

