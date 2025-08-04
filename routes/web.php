<?php

use App\Http\Controllers\FrontController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\TodosController;
use Illuminate\Support\Facades\Route;

Route::controller(FrontController::class)
    ->group(function () {
        Route::get('/', 'index');
    });

Route::controller(LoginController::class)
    ->group(function () {
        Route::post('/login/login', 'login');
        Route::get('/login/logout', 'logout');
    });

Route::controller(TodosController::class)
    ->group(function () {
        Route::post('/todos', 'create');
        Route::post('/todos/{id}', 'update');
        Route::get('/todos/{id}/completed', 'completed');
        Route::get('/todos/{id}/delete', 'delete');

        Route::get('/todos/{id}/alert', 'alert');
    });
