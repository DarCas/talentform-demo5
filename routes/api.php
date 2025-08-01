<?php

use App\Http\Controllers\Api\TodosController;
use Illuminate\Support\Facades\Route;

Route::controller(TodosController::class)
    ->group(function () {
        /**
         * Implementazione CRUD inserimenti nella tabella «todos» del database
         */

        Route::post('/todos', 'create');
        Route::get('/todos', 'read');
        Route::put('/todos/{id}', 'update');
        Route::delete('/todos/{id}', 'delete');
    });
