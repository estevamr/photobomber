<?php

use App\Http\Controllers\UploadPhotoController;
use App\Http\Controllers\AlbumController;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/albums/{albumId?}', function ($albumId=NULL) {
    if(isset($albumId)) {
        return Inertia::render('AlbumDashboard', ['albumId' => $albumId]);    
    }
    return Inertia::render('Albums');
})->middleware(['auth', 'verified'])->name('albums');


Route::middleware('auth')->group(function () {
    Route::post('/photos', UploadPhotoController::class);
});



Route::post('/upload', [UploadPhotoController::class, 'upload']);
Route::get('/images', [UploadPhotoController::class, 'index']);
Route::delete('/images/{id}', [UploadPhotoController::class, 'deleteImage']);

Route::get('/albumsList', [AlbumController::class, 'index']);
Route::get('/albumShow/{id}', [AlbumController::class, 'show']);
Route::post('/albums', [AlbumController::class, 'store']);
Route::post('/addPhotoToAlbum', [AlbumController::class, 'addPhotoToAlbum']);
Route::put('/album/{id}', [AlbumController::class, 'update']);
Route::delete('/albumsList/{id}', [AlbumController::class, 'destroy']);

Route::get('/albumDashboard/{id}', [AlbumController::class, 'dashboard']);

require __DIR__ . '/auth.php';
