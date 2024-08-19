<?php

use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::redirect('/', 'login');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// TODO: to resource
Route::get('/offer-test', function () {
    return Inertia::render('Offer/Show');
})->middleware(['auth', 'verified'])->name('offer-test');


Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::resources([
        'users' => UserController::class,
    ]);
});

//Route::middleware('auth')->group(function () {
//    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
//});

require __DIR__.'/auth.php';
