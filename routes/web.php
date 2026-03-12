<?php

use App\Http\Controllers\ApplicationCategoryController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\ApplicationRoundController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::view('/contact', 'contact')->name('contact');

require __DIR__ . '/auth.php'; // provides /login, /register, etc.


Route::middleware(['auth'])->group(function () {
    Route::redirect('/', '/applications')->name('home');
    Route::redirect('/dashboard', '/applications')->middleware(['auth', 'verified'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/applications/form/{applicationCategory}', [ApplicationController::class, 'showForm'])
        ->name('applications.form');
    Route::resource('applications', ApplicationController::class);

    Route::resource('users', UserController::class);

    Route::resource('rounds', ApplicationRoundController::class)
        ->parameters(['rounds' => 'applicationRound']);

    Route::patch('/categories/{applicationCategory}/toggle-status', [ApplicationCategoryController::class, 'toggleStatus'])
        ->name('categories.toggleStatus');
    Route::resource('categories', ApplicationCategoryController::class)
    ->parameters(['categories' => 'applicationCategory']);
});

Route::get('/api/departments', [UserController::class, 'departmentsByFaculty'])
    ->name('api.departments');
