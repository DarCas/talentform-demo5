<?php

use App\Http\Controllers\FrontController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\TodosController;
use App\Http\Controllers\UsersController;
use App\Http\Middleware\Authenticator;
use Illuminate\Support\Facades\Route;

Route::controller(FrontController::class)
    ->middleware(Authenticator::class)
    ->group(function () {
        Route::get('/', 'index');
    });

Route::controller(LoginController::class)
    ->group(function () {
        Route::get('/login', 'index');

        Route::post('/login/login', 'login');

        Route::get('/login/logout', 'logout')
            ->middleware(Authenticator::class);

        Route::get('/login/recupera-password', 'recuperaPassword');
        Route::post('/login/invia-password', 'inviaPassword');
    });

Route::controller(TodosController::class)
    ->middleware(Authenticator::class)
    ->group(function () {
        Route::post('/todos', 'create');
        Route::post('/todos/{todo}', 'update');
        Route::get('/todos/{todo}/delete', 'delete');

        Route::get('/todos/backup', 'backup');
        Route::get('/todos/{todo}/alert', 'alert');
        Route::get('/todos/{todo}/completed', 'completed');
    });

Route::controller(UsersController::class)
    ->middleware(Authenticator::class)
    ->group(function () {
        Route::post('/users', 'create');
        Route::get('/users', 'read');
        Route::post('/users/{user}', 'update');
        Route::get('/users/{user}/delete', 'delete');

        Route::get('/users/backup', 'backup');
    });
