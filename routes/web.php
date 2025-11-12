<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\ThemeController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\DistributionController;
use App\Http\Controllers\Admin\SupervisorController;
use App\Http\Controllers\Leader\AssignmentController;
use App\Http\Controllers\Student\ThemeController as StudentThemeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// Auth routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Dashboard and protected routes (auth middleware can be added later)
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
// Admin routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/themes', [ThemeController::class, 'index'])->name('themes');
    Route::post('/themes/upload', [ThemeController::class, 'upload'])->name('themes.upload');
    Route::delete('/themes/{id}', [ThemeController::class, 'destroy'])->name('themes.destroy');
    Route::get('/students', [StudentController::class, 'index'])->name('students');
    Route::post('/students/upload', [StudentController::class, 'upload'])->name('students.upload');
    Route::delete('/students/{id}', [StudentController::class, 'destroy'])->name('students.destroy');
    Route::get('/supervisors', [SupervisorController::class, 'index'])->name('supervisors');
    Route::post('/supervisors/upload', [SupervisorController::class, 'upload'])->name('supervisors.upload');
    Route::delete('/supervisors/{id}', [SupervisorController::class, 'destroy'])->name('supervisors.destroy');
    Route::post('/distribute', [DistributionController::class, 'distribute'])->name('distribute');
});

// Group leader routes
Route::prefix('leader')->name('leader.')->group(function () {
    Route::get('/assignments', [AssignmentController::class, 'index'])->name('assignments');
    Route::get('/assign', [AssignmentController::class, 'create'])->name('assign');
    Route::post('/assign', [AssignmentController::class, 'store'])->name('assign.store');
});

// Student routes
Route::prefix('student')->name('student.')->group(function () {
    Route::get('/theme', [StudentThemeController::class, 'index'])->name('theme');
});
