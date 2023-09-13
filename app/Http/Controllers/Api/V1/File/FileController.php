<?php

namespace App\Http\Controllers\Api\V1\File;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\File\UploadFileRequest;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    protected File $file;

    /**
     * @param  File  $file
     */

    public function __construct(File $file)
    {
        $this->file = $file;
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

    public function uploadFileFolder(UploadFileRequest $request , $folderId = null)
    {
        $userShareId = $request->input('user_share_id');
        $dataFiles = [];
        $ids = [];
        try {
            if ($request->hasFile('files'))
            {
                foreach ($request->file('files') as $file)
                {
                    $name = $file->getClientOriginalName();
                    $storage = floor((int) $file->getSize() / 1024);
                    $extension = $file->getClientOriginalExtension();

                    $dataFiles[] =  [
                        'name' => time().$name,
                        'path' => time().$name,
                        'type' => $extension,
                        'user_id' => Auth::id(),
                        'parent_id' => null,
                        'size' => $storage
                    ];
//                    handleUploadFile($file ,Storage::path('public/files') ,$name);

                }
                $this->file->create(collect($dataFiles));
                dd($dataFiles);
            }

        }catch (\Exception $exception)
        {
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
