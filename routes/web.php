<?php

use App\Http\Controllers\Admin\InformationsController;
use App\Http\Controllers\Admin\MainScreenController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Middleware\UserIsAdminMiddleware;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::redirect('/', 'login');

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->middleware(['auth', 'verified'])->name('dashboard');

// TODO: to resource
    Route::get('/offer-test', function () {
        return Inertia::render('Offer/Show');
    })->middleware(['auth', 'verified'])->name('offer-test');


    Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => [UserIsAdminMiddleware::class]], function () {
        Route::resources([
            'users' => UserController::class,
            'main_screen' => MainScreenController::class,
            'informations' => InformationsController::class,
        ]);
        Route::post('/users/handle_block_attempt/{user}', [UserController::class, 'handleBlockAttempt'])->name('users.handle-block-attempt');
        Route::post('/users/handle_admin_attempt/{user}', [UserController::class, 'handleAdminAttempt'])->name('users.handle-admin-attempt');
    });

});

//Route::middleware('auth')->group(function () {
//    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
//});

require __DIR__ . '/auth.php';
