<?php

namespace App\Http\Controllers\Api\V1\File;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\File\UploadFileRequest;
use App\Models\File;
use App\Models\Folder;
use App\Models\User;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    /**
     * @param  File  $file
     */
    protected File $file;

    /**
     * @var User
     */

    protected User $user;

    protected Folder $folder;

    public function __construct(File $file , User $user , Folder  $folder)
    {
        $this->file = $file;
        $this->user = $user;
        $this->folder = $folder;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     *
     */
    public function index()
    {
        //
    }

    /**
     * @param  UploadFileRequest  $request
     * @param $folderId
     * @return \Illuminate\Http\JsonResponse|void
     */

    public function uploadFileFolder(UploadFileRequest $request , $folderId = null)
    {
        $userShareId = $request->input('user_share_ids' ,[]);
        $fileIds = [];
        if ($folderId)
        {
           $folder = $this->folder->findOrFail($folderId);
           if (is_null($folder))
           {
               throw new ValidationException("Folder khong ton tai");
           }

        }
        DB::beginTransaction();
        try {
            if ($request->hasFile('files'))
            {
                foreach ($request->file('files') as $file)
                {
                    $name = time().'-'.$file->getClientOriginalName();
                    $storage = floor((int) $file->getSize() / 1024);
                    $extension = $file->getClientOriginalExtension();
                    $fileInstance =$this->file->create([
                        'name' => $name,
                        'path' => $name,
                        'type' => $extension,
                        'user_id' => Auth::id(),
                        'folder_id' => $folderId,
                        'size' => $storage
                    ]);
                    $fileIds[] = $fileInstance->id;
                    handleUploadFile($file ,Storage::path('public/files') ,$name);
                }
                $users = $this->user->whereIn('id' , $userShareId)->get();
                if ($users->count() > 0)
                {
                    foreach ($users as  $user)
                    {
                        $user->shareUsers()->sync($fileIds);
                    }
                }
                DB::commit();
              return $this->successResponse(null ,'oke' , 201);
            }

            throw new ValidationException('File không hợp lệ');

        }catch (\Exception $exception)
        {
            DB::rollBack();
            $this->errorResponse('error upload files' , 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
