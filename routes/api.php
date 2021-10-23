<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::prefix('auth')->group(function () {
    Route::post('/login', [UserController::class, 'login']);

});

Route::prefix('articles')->group(function () {
    Route::get('/my', [ArticleController::class, 'my'])
        ->middleware('auth');

    Route::post('/', [ArticleController::class, 'store'])
        ->middleware('auth');

    Route::delete('/{id}', [ArticleController::class, 'destroy'])
        ->middleware('auth');
});

Route::post('/users', [UserController::class, 'store']);
