<?php

use App\Http\Controllers\Api\V2\DocumentController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function (){
    Route::prefix('document')->group(function (){
        Route::post('move' , [DocumentController::class ,'moveDocument']);
        Route::post('copy' , [DocumentController::class ,'copyDocument']);
        Route::post('upload-file' , [DocumentController::class ,'uploadFile']);
        Route::post('share' , [DocumentController::class ,'shareDocument']);
        Route::resource('/' , DocumentController::class);
    });
});
