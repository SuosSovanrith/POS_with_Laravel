<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\Admin;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PositionController;

// Auth
Route::get('/', function () {
    return view('auth.login');
});

Route::get('/auth/login', function () {
    return view('auth.login');
});

Route::get('/auth/register', function () {
    return view('auth.register');
});

Route::post('/login', [AuthController::class, 'Login']);
Route::get('/logout', [AuthController::class, 'Logout']);

// Admin Middleware
Route::middleware(Admin::class)->group(function(){
    Route::get('/admin/index', function () {
        return view('admin.index');
    });
    Route::get('/admin/users', [UsersController::class, 'UsersView']);
    Route::get('/admin/position', [PositionController::class, 'PositionView']);
});

// User
Route::post('/adduser', [UsersController::class, 'AddUser']);
Route::post('/updateuser', [UsersController::class, 'UpdateUser']);
Route::post('/deleteuser', [UsersController::class, 'DeleteUser']);

// Position
Route::post('/addposition', [PositionController::class, 'AddPosition']);
Route::post('/updateposition', [PositionController::class, 'UpdatePosition']);
Route::post('/deleteposition', [PositionController::class, 'DeletePosition']);