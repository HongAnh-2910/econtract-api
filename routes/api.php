<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Department\DepartmentController;
    use App\Http\Controllers\Api\V1\File\FileController;
    use App\Http\Controllers\Api\V1\Folder\FolderController;
    use App\Http\Controllers\Api\V1\Member\MemberController;
    use App\Http\Controllers\Api\V1\Menu\MenuController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1/contract')->group(function (){
    Route::post('user/register', [AuthController::class ,'register'])->name('register');
    Route::post('user/login', [AuthController::class ,'login'])->name('login');

    Route::middleware('auth:sanctum')->group(function (){
        Route::get('user', [AuthController::class ,'user']);
        Route::get('menu', [MenuController::class ,'index']);
        Route::resource('department' , DepartmentController::class);
        Route::delete('department/{id}' , [DepartmentController::class ,'destroy']);
        Route::get('department/{department}' , [DepartmentController::class ,'show']);
        Route::patch('department/{department}' , [DepartmentController::class ,'update']);
        Route::post('department/update-permission-department' , [DepartmentController::class ,'updatePermissionDepartment']);

        Route::resource('member' , MemberController::class)->except('update');
        Route::post('member/{user}' , [MemberController::class ,'update']);

        Route::resource('folder' , FolderController::class)->except('store' , 'show');
        Route::post('folder/{id?}' , [FolderController::class ,'store']);
        Route::get('folder/{id?}' , [FolderController::class ,'index']);
        Route::post('folder/share-folder-or-file/{folderIdOrFile}',[FolderController::class ,'shareFolderOrFile']);
        Route::post('folder/download-folder/{folderId}',[FolderController::class ,'downloadFolder']);

        Route::resource('file' , FileController::class);
        Route::post('file/upload/{folderId?}',[FileController::class ,'uploadFileFolder']);
    });
});
