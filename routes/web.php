<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\StepController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\ProfileController;

// Authentification
Auth::routes();

// Réinitialisation de mot de passe
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

// Inscription
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Page d’accueil
Route::get('/', function () {
    return view('welcome');
});

// Routes protégées par authentification
Route::middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Goals (Objectifs)
    Route::resource('goals', GoalController::class);
    Route::post('goals/{goal}/steps/reorder', [GoalController::class, 'reorderSteps'])->name('goals.steps.reorder');
    Route::post('/goals/{goal}/steps', [StepController::class, 'store'])->name('steps.store');
    // Steps (Étapes liées aux objectifs)
    Route::resource('steps', StepController::class)->except(['index', 'show']);

    // Journaux (Journal d'évolution)
    Route::resource('journals', JournalController::class);

    // Profile utilisateur
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/settings', [ProfileController::class, 'settings'])->name('profile.settings');
    Route::put('/profile/settings', [ProfileController::class, 'updateSettings'])->name('profile.settings.update');

    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
