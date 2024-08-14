<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\ResponseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('/v1')->group(function() {

    Route::post('/auth/login', [AuthController::class, 'login']);
    
    
    Route::middleware('auth:sanctum')->group(function() {
        Route::post('/auth/logout', [AuthController::class, 'logout']);


        Route::resource('forms', FormController::class);

        Route::post('/forms/{slug}/questions', [QuestionController::class, 'store']);
        Route::delete('/forms/{slug}/questions/{id}', [QuestionController::class, 'destroy']);
        
        Route::post('/forms/{slug}/responses', [ResponseController::class, 'store']);
        Route::get('/forms/{slug}/responses', [ResponseController::class, 'index']);
        

    });

});
