<?php

namespace App\Http\Controllers\Api\V1\Folder;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Folder\CreateFolderRequest;
use App\Http\Resources\V1\File\FileResource;
use App\Http\Resources\V1\Folder\FolderResource;
use App\Models\File;
use App\Models\Folder;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class FolderController extends Controller
{
    /**
     * @var Folder
     */
    protected Folder $folder;

    /**
     * @var File
     */

    protected File $file;

    public function __construct(Folder $folder , File $file)
    {
        $this->folder = $folder;
        $this->file = $file;
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($id = null)
    {
        if ($id) {
            $folder = $this->folder->ById($id)->first();
            if (is_null($folder)) {
                throw new ValidationException('Folder không tồn tại', 422);
            }
        }
        $folders = $this->folder->where('parent_id', $id)
                                ->ByUserIdOrUserIdShare()
                                ->get();
        $files   = $this->file->with('user', 'folder')->where('folder_id', $id)
                              ->ByUserIdOrUserIdShare()
                              ->get();
        $data    = [
            'folders' => FolderResource::collection($folders->load('user', 'parent')),
            'files'   => FileResource::collection($files)
        ];
        return response()->json(['data' => $data]);
    }

    /**
     * @param  CreateFolderRequest  $request
     * @param $id
     * @return FolderResource
     */
    public function store(CreateFolderRequest $request , $id = null)
    {
        $name = $request->input('name');
        $data = [
            'name'      => $name,
            'user_id'   => Auth::id(),
            'parent_id' => ! empty($id) ? $id : null,
            'slug'      => Str::slug($name)
        ];
        if ($id) {
            $folder = $this->folder->ById($id)->first();
            if (is_null($folder)) {
                throw new ValidationException('Folder không tồn tại', 422);
            }
        }
        $folder = $this->folder->create($data);
        return new FolderResource($folder->load('user' , 'parent'));
    }

    public function shareFolderOrFile($folderId)
    {
        dd($folderId);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        dd('13');
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
