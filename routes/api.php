<?php

use App\Http\Controllers\AlbumCompilationWebhookController;
use App\Http\Controllers\UploadPhotoController;
use App\Http\Controllers\AlbumController;
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

// Route::post('/webhooks/compilation', AlbumCompilationWebhookController::class);
Route::post('/upload', [UploadPhotoController::class, 'upload']);
Route::post('/album', [AlbumController::class, 'store']);
Route::get('/album', [AlbumController::class, 'index']);

// Route::get('/upload', [UploadPhotoController::class, 'index']);