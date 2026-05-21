<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BookController;

// ── public routes ──
Route::prefix('auth')->group(function () {
    Route::any('/register', [AuthController::class, 'register'])
        ->middleware('check.method:POST');

    Route::any('/login', [AuthController::class, 'login'])
        ->middleware('check.method:POST');
});

// ── protected routes ──


// routes/api.php

Route::middleware('jwt.verify')->group(function () {

    Route::any('/auth/profile', [AuthController::class, 'profile'])
        ->middleware('check.method:GET');

    Route::any('/auth/logout', [AuthController::class, 'logout'])
        ->middleware('check.method:POST');

    // explicitly add middleware to each book route
    Route::get('/books',             [BookController::class, 'index']);
    Route::post('/books',            [BookController::class, 'store']);
    Route::get('/books/{id}',        [BookController::class, 'show']);
    Route::put('/books/{id}',        [BookController::class, 'update']);
    Route::delete('/books/{id}',     [BookController::class, 'destroy']);
});
