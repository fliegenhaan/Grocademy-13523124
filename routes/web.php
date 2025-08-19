<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\CertificateController;

Route::get('/', function () {
    return view('welcome');
});


Route::middleware('guest')->group(function () {
    Route::get('register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function() {
        // Ini adalah halaman contoh setelah berhasil login
        return view('dashboard');
    })->name('dashboard');
    
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/my-courses', [CourseController::class, 'myCourses'])->name('courses.my');
    Route::get('/courses/{course}/modules', [ModuleController::class, 'index'])->name('modules.index');
    Route::post('/modules/{module}/complete', [ModuleController::class, 'complete'])->name('modules.complete');
    Route::get('/modules/{module}', [ModuleController::class, 'show'])->name('modules.show');
    Route::get('/courses/{course}/certificate', [CertificateController::class, 'download'])->name('certificate.download');
});

Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');
Route::post('/courses/{course}/buy', [CourseController::class, 'buy'])->name('courses.buy')->middleware('auth');