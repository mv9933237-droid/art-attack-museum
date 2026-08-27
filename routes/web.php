<?php

use App\Http\Controllers\ArtistController;
use App\Http\Controllers\ArtworkArtistController;
use App\Http\Controllers\ArtworkController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ExhibitionArtworkController;
use App\Http\Controllers\ExhibitionController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\MovementController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\SalesController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/artists', [ArtistController::class, 'index']);
Route::post('/artists', [ArtistController::class, 'store']);
Route::get('/artists/{artist}', [ArtistController::class, 'show']);

Route::get('/artworks', [ArtworkController::class, 'index']);
Route::post('/artworks', [ArtworkController::class, 'store']);
Route::get('/artworks/{artwork}', [ArtworkController::class, 'show']);
Route::put('/artworks/{artwork}', [ArtworkController::class, 'update']);
Route::delete('/artworks/{artwork}', [ArtworkController::class, 'destroy']);
Route::put('/artworks/{artwork}/status', [ArtworkController::class, 'changeStatus']);

Route::get('/artworks/{artwork}/artists', [ArtworkArtistController::class, 'index']);
Route::post('/artworks/{artwork}/artists', [ArtworkArtistController::class, 'store']);
Route::delete('/artworks/{artwork}/artists/{artist}', [ArtworkArtistController::class, 'destroy']);
Route::post('/artworks/{artwork}/unknown-author', [ArtworkArtistController::class, 'assignUnknown']);

Route::get('/artworks/{artwork}/movements', [MovementController::class, 'history']);
Route::get('/artworks/{artwork}/exhibitions', [ArtworkController::class, 'exhibitions']);
Route::post('/movements', [MovementController::class, 'store']);

Route::get('/locations', [LocationController::class, 'index']);
Route::post('/locations', [LocationController::class, 'store']);
Route::get('/locations/{location}', [LocationController::class, 'show']);
Route::put('/locations/{location}', [LocationController::class, 'update']);
Route::get('/locations/{location}/artworks', [LocationController::class, 'artworks']);

Route::get('/exhibitions', [ExhibitionController::class, 'index']);
Route::post('/exhibitions', [ExhibitionController::class, 'store']);
Route::get('/exhibitions/{exhibition}', [ExhibitionController::class, 'show']);
Route::put('/exhibitions/{exhibition}', [ExhibitionController::class, 'update']);
Route::get('/exhibitions/{exhibition}/artworks', [ExhibitionController::class, 'artworks']);

Route::post('/exhibitions/{exhibition}/artworks', [ExhibitionArtworkController::class, 'store']);
Route::delete('/exhibitions/{exhibition}/artworks/{artwork}', [ExhibitionArtworkController::class, 'destroy']);

Route::get('/customers', [CustomerController::class, 'index']);
Route::post('/customers', [CustomerController::class, 'store']);
Route::get('/customers/{customer}', [CustomerController::class, 'show']);

Route::get('/reservations', [ReservationController::class, 'index']);
Route::post('/reservations', [ReservationController::class, 'store']);
Route::get('/reservations/{reservation}', [ReservationController::class, 'show']);
Route::post('/reservations/{reservation}/cancel', [ReservationController::class, 'cancel']);

Route::get('/sales', [SalesController::class, 'index']);
Route::post('/sales', [SalesController::class, 'store']);
Route::get('/sales/{sale}', [SalesController::class, 'show']);
Route::put('/sales/{sale}/confirm', [SalesController::class, 'confirm']);
Route::put('/sales/{sale}/annul', [SalesController::class, 'annul']);

Route::get('/sales/{sale}/payments', [PaymentController::class, 'index']);
Route::post('/sales/{sale}/payments', [PaymentController::class, 'store']);
