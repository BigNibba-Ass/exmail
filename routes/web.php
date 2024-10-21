<?php

use App\Http\Controllers\Admin\InformationsController;
use App\Http\Controllers\Admin\MainScreenController;
use App\Http\Controllers\Admin\OfferController as AdminOfferController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OfferController;
use App\Http\Middleware\UserIsAdminMiddleware;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::redirect('/', 'login');

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/calculate', [DashboardController::class, 'calculate'])->name('calculate');

    Route::resource('offers', OfferController::class)->middleware(['auth', 'verified']);

    Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => [UserIsAdminMiddleware::class]], function () {
        Route::get('/main_screen', [MainScreenController::class, 'index'])->name('main-screen');
        Route::resources([
            'users' => UserController::class,
            'services' => ServiceController::class,
            'companies' => CompanyController::class,
            'informations' => InformationsController::class,
            'offers' => AdminOfferController::class,
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
