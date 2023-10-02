<?php

use App\Http\Controllers\Api\V1\Application\ApplicationController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Department\DepartmentController;
    use App\Http\Controllers\Api\V1\File\FileController;
    use App\Http\Controllers\Api\V1\Document\DocumentController;
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
        Route::prefix('department')->group(function () {
            Route::delete('{id}', [DepartmentController::class, 'destroy']);
            Route::get('{department}', [DepartmentController::class, 'show']);
            Route::patch('{department}', [DepartmentController::class, 'update']);
            Route::post('update-permission-department', [DepartmentController::class, 'updatePermissionDepartment']);
        });

        Route::resource('member' , MemberController::class)->except('update');
        Route::post('member/{user}' , [MemberController::class ,'update']);


        Route::prefix('document')->group(function () {
            Route::post('share-folder-or-file/{folderIdOrFileId}', [DocumentController::class, 'shareFolderOrFile']);
            Route::post('download-folder-or-file/{folderIdOrFileId}',
                [DocumentController::class, 'downloadFolderOrFile']);
            Route::patch('rename-folder/{folder}',
                [DocumentController::class, 'renameFolder']);
            Route::patch('moved-folder-or-file/{folderIdOrFileId}',
                [DocumentController::class, 'movedFolderOrFile']);
            Route::delete('delete-folder-or-file',
                [DocumentController::class, 'deleteFolderOrFile']);
            Route::post('export-document',
                [DocumentController::class, 'exportDocument']);
            Route::post('{id?}', [DocumentController::class, 'store']);
            Route::get('{id?}', [DocumentController::class, 'index']);
        });

        Route::resource('file' , FileController::class);
        Route::post('file/upload/{folderId?}',[FileController::class ,'uploadFileFolder']);

        Route::prefix('application')->group(function (){
            Route::post('store', [ApplicationController::class ,'store']);
            Route::post('update-state/{application}', [ApplicationController::class ,'updateState']);
        });
    });
});
