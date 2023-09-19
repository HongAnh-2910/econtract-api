<?php

namespace App\Http\Controllers\Api\V1\Folder;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Folder\CreateFolderRequest;
use App\Http\Requests\V1\Folder\ShareFolderRequest;
use App\Http\Resources\V1\File\FileResource;
use App\Http\Resources\V1\Folder\FolderResource;
use App\Http\Resources\V1\FolderFileResource;
use App\Jobs\ZipFileOrFolderDownload;
use App\Models\File;
use App\Models\Folder;
use App\Models\User;
use App\Services\FolderService\FolderServiceInterface;
use DomainException;
use Dotenv\Exception\ValidationException;
use Exception;
use Illuminate\Bus\Batch;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Predis\Client;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
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
     * @return AnonymousResourceCollection
     */
    public function index($id = null)
    {
        if ($id) {
            $folder = $this->folder->ById($id)->first();
            if (is_null($folder)) {
                throw new ValidationException('Folder không tồn tại', 422);
            }
        }
        $folders      = $this->folder->where('parent_id', $id)->with('user', 'parent')
                                     ->ByUserIdOrUserIdShare()
                                     ->get();
        $files        = $this->file->with('user', 'folder')->where('folder_id', $id)
                                   ->ByUserIdOrUserIdShare()
                                   ->get();
        $folders      = $folders->map(function ($item) {
            $item['folder_id'] = $item->id;
            unset($item->id);
            return $item;
        });
        $foldersFiles = $folders->push($files)->flatten()->sortBy('created_at')->values()->all();
        return FolderFileResource::collection($foldersFiles);
    }

    /**
     * @param  CreateFolderRequest  $request
     * @param $id
     * @return FolderResource
     */
    public function store(CreateFolderRequest $request , $id = null)
    {
        $name        = $request->input('name');
        $data        = [
            'name'      => $name,
            'user_id'   => Auth::id(),
            'parent_id' => ! empty($id) ? $id : null,
            'slug'      => Str::slug($name)
        ];
        $userIdShare = [];
        if ($id) {
            $folder      = $this->folder->ById($id)->first();
            $userIdShare = $folder->users()->get()->pluck('id');
            if (is_null($folder)) {
                throw new ValidationException('Folder không tồn tại', 422);
            }
        }
        $folder = $this->folder->create($data);
        if (count($userIdShare) > 0) {
            $folder->users->attach($userIdShare);
        }
        return new FolderResource($folder->load('user', 'parent'));
    }

    /**
     * @param  ShareFolderRequest  $request
     * @param $folderId
     * @return JsonResponse|void
     */

    public function shareFolderOrFile(ShareFolderRequest $request, $folderOrFileId)
    {
        $shareUserIds = $request->input('user_share_ids', []);
        $typeCheck    = $request->input('type_check');
        $users        = $this->user->whereIn('id', $shareUserIds);
        if ($typeCheck == 'folder') {
            $folder = $this->folder->with('treeChildren', 'parent')->ById($folderOrFileId)->first();
            if (is_null($folder)) {
                throw new ValidationException('Folder không tồn tại', 422);
            }
            if ($users->count() != count($shareUserIds)) {
                throw new ValidationException('UserId không tồn tại', 422);
            }
            $folderIds = dataTree($folder->treeChildren, $folderOrFileId)->pluck('id')->merge(+$folderOrFileId);
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

            } catch (Exception $exception) {
                DB::rollBack();
                return $this->errorResponse('share file error', 500);
            }
        }
//        share File
        $file = $this->file->where('id', $folderOrFileId)->first();
        if (is_null($file))
        {
            throw new ValidationException('File không tồn tại', 422);
        }
        if ($file->users()->get()->whereIn('id' , $shareUserIds)->count() > 0) {
            throw new ValidationException('User được chọn đã có file này !', 422);
        }
        DB::beginTransaction();
        try {
            $file->users()->sync($shareUserIds);
            DB::commit();
            return $this->successResponse(null, 'oke', 201);
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->errorResponse('share file error', 500);
        }
    }

    /**
     * @param $folderId
     * @return BinaryFileResponse
     * @throws BindingResolutionException
     */
    public function downloadFolder($folderId)
    {
        $currentFolder = $this->folder->where('id', $folderId)->first();
        if (is_null($currentFolder)) {
            throw new ValidationException('Folder không tồn tại', 500);
        }
        $nameFolderZip = time().'-'.$currentFolder->name.'.zip';
        try {
            $zip     = new ZipArchive();
            $path    = '';
            $zipFile = app()->make(FolderServiceInterface::class);
            if ($zip->open(public_path($nameFolderZip), ZipArchive::CREATE) === true) {
                $zipFile->zipToFileAndFolder($zip, $path, $currentFolder);
                $zip->close();
            }
            return response()->download($nameFolderZip)->deleteFileAfterSend();

        } catch (DomainException $exception) {
            throw new DomainException($exception->getMessage(), 500);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        dd('13');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
