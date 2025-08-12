<?php

use App\Http\Controllers\Api\Todos;
use App\Http\Controllers\Api\TodosController;
use App\Http\Controllers\Api\Users;
use App\Http\Controllers\Api\UsersController;
use App\Http\Middleware;
use Illuminate\Support\Facades\Route;

Route::controller(TodosController::class)
    ->group(function () {
        /**
         * Implementazione CRUD inserimenti nella tabella «todos» del database
         */

        Route::post('/todos', 'create');
        Route::get('/todos', 'read');
        Route::put('/todos/{todo}', 'update');
        Route::delete('/todos/{todo}', 'delete');
    });

Route::controller(Todos\BackupController::class)
    ->middleware(Middleware\File::class . ':todos')
    ->group(function () {
        Route::get('/todos/backup', 'read');
        Route::delete('/todos/backup/{filename}', 'delete');

        Route::get('/todos/backup/{filename}', 'download');
    });

Route::controller(UsersController::class)
    ->group(function () {
        Route::post('/users', 'create');
        Route::get('/users', 'read');
        Route::put('/users/{user}', 'update');
        Route::delete('/users/{user}', 'delete');
    });

Route::controller(Users\BackupController::class)
    ->middleware(Middleware\File::class . ':users')
    ->group(function () {
        Route::get('/users/backup', 'read');
        Route::delete('/users/backup/{filename}', 'delete');

        Route::get('/users/backup/{filename}', 'download');
    });
