<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\Admin;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CartController;

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
    Route::get('/admin/products', [ProductsController::class, 'ProductsView']);
    Route::get('/admin/category', [CategoryController::class, 'CategoryView']);
    Route::get('/admin/supplier', [SupplierController::class, 'SupplierView']);
    Route::get('/admin/customer', [CustomerController::class, 'CustomerView']);
    Route::get('/admin/cart', [CartController::class, 'CartView']);
});

// User
Route::post('/adduser', [UsersController::class, 'AddUser']);
Route::post('/updateuser', [UsersController::class, 'UpdateUser']);
Route::post('/deleteuser', [UsersController::class, 'DeleteUser']);

// Position
Route::post('/addposition', [PositionController::class, 'AddPosition']);
Route::post('/updateposition', [PositionController::class, 'UpdatePosition']);
Route::post('/deleteposition', [PositionController::class, 'DeletePosition']);

// Products
Route::post('/addproduct', [ProductsController::class, 'AddProduct']);
Route::post('/updateproduct', [ProductsController::class, 'UpdateProduct']);
Route::post('/deleteproduct', [ProductsController::class, 'DeleteProduct']);

// Category
Route::post('/addcategory', [CategoryController::class, 'AddCategory']);
Route::post('/updatecategory', [CategoryController::class, 'UpdateCategory']);
Route::post('/deletecategory', [CategoryController::class, 'DeleteCategory']);

// Supplier
Route::post('/addsupplier', [SupplierController::class, 'AddSupplier']);
Route::post('/updatesupplier', [SupplierController::class, 'UpdateSupplier']);
Route::post('/deletesupplier', [SupplierController::class, 'DeleteSupplier']);

// Customer
Route::post('/addcustomer', [CustomerController::class, 'AddCustomer']);
Route::post('/updatecustomer', [CustomerController::class, 'UpdateCustomer']);
Route::post('/deletecustomer', [CustomerController::class, 'DeleteCustomer']);

// Cart
Route::post('/addcart', [CartController::class, 'AddCart']);
Route::post('/updatecartquantity', [CartController::class, 'UpdateCartQuantity']);
Route::post('/deletecart', [CartController::class, 'DeleteCart']);
Route::post('/clearcart', [CartController::class, 'ClearCart']);
