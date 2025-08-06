<?php

use App\Http\Controllers\FrontController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\TodosController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

Route::controller(FrontController::class)
    ->group(function () {
        Route::get('/', 'index');
    });

Route::controller(LoginController::class)
    ->group(function () {
        Route::post('/login/login', 'login');
        Route::get('/login/logout', 'logout');

        Route::get('/login/recupera-password', 'recuperaPassword');
        Route::post('/login/invia-password', 'inviaPassword');
    });

Route::controller(TodosController::class)
    ->group(function () {
        Route::post('/todos', 'create');
        Route::post('/todos/{id}', 'update');
        Route::get('/todos/{id}/delete', 'delete');

        Route::get('/todos/backup', 'backup');
        Route::get('/todos/{id}/alert', 'alert');
        Route::get('/todos/{id}/completed', 'completed');
    });

Route::controller(UsersController::class)
    ->group(function () {
        Route::post('/users', 'create');
        Route::get('/users', 'read');
        Route::post('/users/{id}', 'update');
        Route::get('/users/{id}/delete', 'delete');

        Route::get('/users/backup', 'backup');
    });
