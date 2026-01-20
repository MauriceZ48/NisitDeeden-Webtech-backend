<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
})->middleware(['auth'])->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::get('/contact', function () {
    return view('contact');
});

//
//Route::get('/applications/dashboard', function () {
//    return view('applications.dashboard');
//});
//Route::get('/applications/dashboard', function () {
//    return view('admin.applications.dashboard');
//});

Route::redirect('/', '/applications');

Route::resource('applications', ApplicationController::class);
Route::resource('users', UserController::class);

//Route::get('/user', function () {
//    return view('users.index');
//});
//
//Route::get('/user/create', function () {
//    return view('users.form');
//});

Route::get('/api/departments', [UserController::class, 'departmentsByFaculty'])
    ->name('api.departments');

