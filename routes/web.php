<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\PasswordResetController;
use Illuminate\Support\Facades\Route;

// MAIN ROUTES`
// views
Route::get('/', function() { return view('index'); })
    ->middleware('auth')
    ->name('index');

// AUTHENTICATION ROUTES
// views
Route::get('/register', [AuthController::class, 'showRegistrationForm'])
    ->name('register');

Route::get('/login', [AuthController::class, 'showLoginForm'])
    ->name('login');

// PASSWORD RESET ROUTES
// views
Route::get('/forgot-password', [PasswordResetController::class, 'showLinkRequestForm'])
    ->name('password.request');

Route::prefix('api')->group(function () {
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])
        ->name('api.password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])
        ->name('api.password.update');
});

// SETTINGS ROUTES
// views
Route::get('/settings', [SettingController::class, 'showSettings'])
    ->middleware('auth')
    ->name('settings');

// GROUPS ROUTES
// views
Route::get('/groups', [GroupController::class, 'index'])
    ->middleware('auth')
    ->name('groups.index');

// EVENT ROUTES
// get
Route::get('/events', [EventController::class, 'index'])
    ->middleware('auth')
    ->name('events.index');

Route::get('/event/create', [EventController::class, 'create'])
    ->middleware('auth')
    ->name('event.create');

Route::get('/event/{event}', [EventController::class, 'show'])
    ->middleware('auth')
    ->where('event', '[0-9]+')
    ->name('event.show');

// NOTIFICATION ROUTES
// views
Route::get('/notifications', [NotificationController::class, 'index'])
    ->middleware('auth')
    ->name('notifications.index');

// SETTINGS ROUTES
// views
Route::get('/settings', [SettingController::class, 'showSettings'])
    ->name('settings.index')
    ->middleware('auth');
