<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PasswordResetController;
use Illuminate\Support\Facades\Route;

// AUTHENTICATION ROUTES
// post
Route::post('/register', [AuthController::class, 'register'])
    ->middleware('web')
    ->name('api.register');
Route::post('/login', [AuthController::class, 'login'])
    ->middleware('web')
    ->name('api.login');
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('web')
    ->name('api.logout');

// USER ROUTES
// patch
Route::patch('/user/profile', [UserController::class, 'updateProfile'])
    ->name('api.user.profile.update')
    ->middleware('auth:sanctum');
Route::patch('/user/settings', [SettingController::class, 'update']) // TODO change deceiving method names
    ->name('api.user.settings.update')
    ->middleware('auth:sanctum');

// get
Route::get('/users', [UserController::class, 'search'])
    ->name('api.user.search'); // List users

//delete
Route::delete('/user', [UserController::class, 'delete'])
    ->middleware(['web', 'auth:sanctum'])
    ->name('api.user.delete');
// PASSWORD RESET ROUTES
// get
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])
    ->name('api.password.reset');
// post
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])
    ->name('api.password.email');

Route::post('/reset-password', [PasswordResetController::class, 'reset'])
    ->name('api.password.update');
    
// EVENT ROUTES
// get
Route::get('/events', [EventController::class, 'search'])
    ->name('api.event.search'); // List events

// post
Route::post('/event', [EventController::class, 'store'])
    ->middleware('auth:sanctum')
    ->name('api.event.store');

// patch
Route::patch('/event/{event}', [EventController::class, 'update'])
    ->middleware('auth:sanctum')
    ->name('api.event.update');
Route::patch('/event/{event}/attendance', [EventController::class, 'setAttendance'])
    ->middleware('auth:sanctum')
    ->name('api.event.setAttendance');

// GROUP ROUTES
// get
Route::get('/groups', [GroupController::class, 'search'])
    ->middleware('auth:sanctum')
    ->name('api.group.search'); // List groups

// post
Route::post('/group', [GroupController::class, 'store'])
    ->middleware('auth:sanctum')
    ->name('api.group.store');
Route::post('/group/{group}/members', [GroupController::class, 'addMembers'])
    ->middleware('auth:sanctum')
    ->name('api.group.members.add');

// patch
Route::patch('/group/{group}', [GroupController::class, 'update'])
    ->middleware('auth:sanctum')
    ->name('api.group.update');

// delete
Route::delete('/group/{group}', [GroupController::class, 'destroy'])
    ->middleware('auth:sanctum')
    ->name('api.group.delete');
Route::delete('/group/{group}/members', [GroupController::class, 'destroyMembers'])
    ->middleware('auth:sanctum')
    ->name('api.group.members.destroy');

// NOTIFICATION ROUTES
// patch
Route::get('notifications', [NotificationController::class, 'list'])
    ->middleware('auth:sanctum')
    ->name('api.notifications.list');
Route::patch('/notifications/{id}/read', [NotificationController::class, 'read']) // Mark one notification as read
    ->middleware('auth:sanctum')
    ->name('api.notifications.read');
Route::patch('/notifications/read-all', [NotificationController::class, 'readAll']) // Mark all notifications as read
    ->middleware('auth:sanctum')
    ->name('api.notifications.readAll');

// GENERAL ROUTES
// get
Route::get('/users-and-groups', [EventController::class, 'searchUsersAndGroups'])
    ->middleware('auth:sanctum')
    ->name('api.search.users-and-groups');
