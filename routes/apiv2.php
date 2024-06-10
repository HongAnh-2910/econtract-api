<?php

use App\Http\Controllers\Api\V2\DocumentController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function (){
    Route::prefix('document')->group(function (){
        Route::post('move' , [DocumentController::class ,'moveDocument']);
        Route::post('copy' , [DocumentController::class ,'copyDocument']);
        Route::resource('/' , DocumentController::class);
    });
});
