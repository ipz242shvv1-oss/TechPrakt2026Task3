<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProductController;
use App\Http\Controllers\TestListingController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('products', ProductController::class);

Route::get('/listings', [
    TestListingController::class,
    'getListings'
]);

Route::get('/listings/{id}', [
    TestListingController::class,
    'getListingById'
]);

Route::post('/listings', [
    TestListingController::class,
    'createListing'
])->withoutMiddleware([VerifyCsrfToken::class]);

Route::patch('/listings/{id}', [
    TestListingController::class,
    'updateListing'
])->withoutMiddleware([VerifyCsrfToken::class]);

Route::delete('/listings/{id}', [
    TestListingController::class,
    'deleteListing'
])->withoutMiddleware([VerifyCsrfToken::class]);
