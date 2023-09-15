<?php

namespace App\Http\Controllers\Api\V1\Folder;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Folder\CreateFolderRequest;
use App\Http\Requests\V1\Folder\ShareFolderRequest;
use App\Http\Resources\V1\File\FileResource;
use App\Http\Resources\V1\Folder\FolderResource;
use App\Models\File;
use App\Models\Folder;
use App\Models\User;
use App\Services\FolderService\FolderServiceInterface;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

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

    /**
     * @var User
     */

    protected User $user;

    public function __construct(Folder $folder , File $file , User $user)
    {
        $this->folder = $folder;
        $this->file = $file;
        $this->user = $user;
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

    /**
     * @param  ShareFolderRequest  $request
     * @param $folderId
     * @return \Illuminate\Http\JsonResponse|void
     */

    public function shareFolder(ShareFolderRequest $request, $folderId)
    {
        $shareUserIds = $request->input('user_share_ids', []);
        $folder       = $this->folder->with('treeChildren', 'parent')->ById($folderId)->first();
        $users        = $this->user->whereIn('id', $shareUserIds);
        if (is_null($folder)) {
            throw new ValidationException('Folder không tồn tại', 422);
        }
        if ($users->count() != count($shareUserIds)) {
            throw new ValidationException('UserId không tồn tại', 422);
        }
        $folderIds = dataTree($folder->treeChildren, $folderId)->pluck('id')->merge(+$folderId);
        $filesIds  = $this->file->whereIn('folder_id', $folderIds)->get()->pluck('id');
        DB::beginTransaction();
        try {
            foreach ($users->get() as $user) {
                if (count($folderIds) > 0) {
                    $user->folders()->attach($folderIds);
                }
                if (count($filesIds) > 0) {
                    $user->files()->attach($filesIds);
                }
            }
            DB::commit();
           return $this->successResponse(null, 'oke', 201);

        } catch (\Exception $exception) {
            DB::rollBack();
            $this->errorResponse('share file error', 500);
        }
    }

    public function downloadFolder($folderId)
    {
        $currentFolder = $this->folder->where('id', $folderId)->first();
        if (is_null($currentFolder)) {
            throw new ValidationException('Folder không tồn tại', 500);
        }
        $nameFolderZip = time().'-'.$currentFolder->name.'.zip';
        $zip           = new ZipArchive();

        $path    = '';
        $zipFile = app()->make(FolderServiceInterface::class);
        if ($zip->open(public_path($nameFolderZip), ZipArchive::CREATE) === true) {
            $zipFile->zipToFileAndFolder($zip, $path, $currentFolder);
            $zip->close();
        }
        return response()->download($nameFolderZip)->deleteFileAfterSend(true);
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
