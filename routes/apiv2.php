<?php

use App\Http\Controllers\Api\V2\Document;
use Illuminate\Support\Facades\Route;

Route::prefix('document')->group(function (){
    Route::resource('/' , Document::class);
});
