<?php

use App\Http\Controllers\FrontController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Todos;
use App\Http\Controllers\TodosController;
use App\Http\Controllers\Users;
use App\Http\Controllers\UsersController;
use App\Http\Middleware;
use Illuminate\Support\Facades\Route;

Route::controller(FrontController::class)
    ->middleware(Middleware\Authenticator::class)
    ->group(function () {
        Route::get('/', 'index');
    });

Route::controller(LoginController::class)
    ->group(function () {
        Route::get('/login', 'index');

        Route::post('/login/login', 'login');

        Route::get('/login/logout', 'logout')
            ->middleware(Middleware\Authenticator::class);

        Route::get('/login/recupera-password', 'recuperaPassword');
        Route::post('/login/invia-password', 'inviaPassword');
    });

Route::controller(TodosController::class)
    ->middleware(Middleware\Authenticator::class)
    ->group(function () {
        Route::get('/todos', 'index');

        Route::post('/todos', 'create');
        Route::post('/todos/{todo}', 'update');
        Route::get('/todos/{todo}/delete', 'delete');

        Route::get('/todos/{todo}/alert', 'alert');
        Route::get('/todos/{todo}/completed', 'completed');
    });

Route::controller(Todos\BackupController::class)
    ->group(function () {
        Route::middleware(Middleware\Authenticator::class)
            ->group(function () {
                Route::get('/todos/backup', 'index');
                Route::post('/todos/backup/delete', 'delete');
            });

        Route::middleware(Middleware\File::class . ':todos')
            ->group(function () {
                Route::get('/todos/backup/{filename}/delete', 'delete')
                    ->middleware(Middleware\Authenticator::class);

                Route::get('/todos/backup/{filename}/download', 'download');
            });
    });

Route::controller(UsersController::class)
    ->middleware(Middleware\Authenticator::class)
    ->group(function () {
        Route::post('/users', 'create');
        Route::get('/users', 'read');
        Route::post('/users/{user}', 'update');
        Route::get('/users/{user}/delete', 'delete');
    });

Route::controller(Users\BackupController::class)
    ->group(function () {
        Route::middleware(Middleware\Authenticator::class)
            ->group(function () {
                Route::get('/users/backup', 'index');
                Route::post('/users/backup/delete', 'delete');
            });

        Route::middleware(Middleware\File::class . ':users')
            ->group(function () {
                Route::get('/users/backup/{filename}/delete', 'delete')
                    ->middleware(Middleware\Authenticator::class);

                Route::get('/users/backup/{filename}/download', 'download');
            });
    });
