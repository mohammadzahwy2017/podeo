<?php

use App\Http\Controllers\EpisodeController;
use App\Http\Controllers\PodcastController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', [UserController::class, 'index'])->name('user.login');

Route::group([
    'middleware' => ['XssSanitizer', 'auth:sanctum']
], function () {

    Route::delete('logout', [UserController::class, 'logout'])
        ->name('user.logout');
    Route::delete('revoke-all-tokens', [UserController::class, 'revokeAllTokens'])
        ->name('user.revoke-all-tokens');
    Route::delete('revoke-tokens-except-current', [UserController::class, 'revokeTokensExceptCurrent'])
        ->name('user.revoke-tokens-except-current');

    Route::group([
        'as' => 'podcast.',
        'prefix' => 'podcast',
    ], function () {
        Route::apiResource('episode', EpisodeController::class)
            ->except(['create', 'edit']);
    });

    Route::apiResource('podcast', PodcastController::class)
        ->except(['create', 'edit']);
});
