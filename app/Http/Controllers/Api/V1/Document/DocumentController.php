<?php

namespace App\Http\Controllers\Api\V1\Document;

use App\Enums\DocumentStatus;
use App\Enums\Status;
use App\Enums\TypeDelete;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Document\CreateFolderRequest;
use App\Http\Requests\V1\Document\DeleteFolderOrFileRequest;
use App\Http\Requests\V1\Document\ListFolderAndFileRequest;
use App\Http\Requests\V1\Document\RenameFolderRequest;
use App\Http\Requests\V1\Document\TypeCheckFolderOrFileRequest;
use App\Http\Requests\V1\Document\ShareFolderRequest;
use App\Http\Requests\V1\Document\TypeExportDocumentRequest;
use App\Http\Resources\V1\Folder\FolderResource;
use App\Http\Resources\V1\FolderFileResource;
use App\Jobs\ZipFileOrFolderDownload;
use App\Models\File;
use App\Models\Folder;
use App\Models\User;
use App\Services\FolderService\FolderServiceInterface;
use Bschmitt\Amqp\Facades\Amqp;
use DomainException;
use Dotenv\Exception\ValidationException;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;
use Psr\Http\Message\ResponseInterface;

class DocumentController extends Controller
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
     * @param ListFolderAndFileRequest $request
     * @param $id
     * @return AnonymousResourceCollection
     */
    public function index(ListFolderAndFileRequest $request ,$id = null)
    {
        $status = $request->input('status');
        if ($id) {
            $folder = $this->folder->ById($id)->first();
            if (is_null($folder)) {
                throw new ValidationException('Folder không tồn tại', 422);
            }
        }
        $folders      = $this->folder->GetByParent($id)->with('user', 'parent' ,'users')
                                                       ->FilterStatus($status)
                                                       ->get();
        $files        = $this->file->with('user', 'folder' ,'users')
                                   ->FilterStatus($status)
                                   ->where('folder_id', $id)
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
        if ($id) {
            $folder      = $this->folder->ById($id)->first();
            if (is_null($folder)) {
                throw new ValidationException('Folder không tồn tại', 422);
            }
        }
        $folder = $this->folder->create($data);
        return new FolderResource($folder->load('user', 'parent'));
    }

    /**
     * @param  ShareFolderRequest  $request
     * @param $folderId
     * @return JsonResponse|void
     */

    public function shareFolderOrFile(ShareFolderRequest $request, $folderIdOrFileId)
    {
        $shareUserIds = collect($request->input('user_share_ids', []))->filter();
        $typeCheck    = $request->input('type_check');
        $users        = $this->user->whereIn('id', $shareUserIds);
        if ($typeCheck == Status::FOLDER) {
            $folder = $this->folder->with('treeChildren', 'parent')->ById($folderIdOrFileId)->first();
            if (is_null($folder)) {
                throw new ValidationException('Folder không tồn tại', 422);
            }
            if ($users->count() != count($shareUserIds)) {
                throw new ValidationException('UserId không tồn tại', 422);
            }
            $folderIds = dataTree($folder->treeChildren, $folderIdOrFileId)->pluck('id')->merge(+$folderIdOrFileId);
            $files  = $this->file->whereIn('folder_id', $folderIds)->get();
            $filesIds = $files->pluck('id');
            DB::beginTransaction();
            try {
                if (count($folderIds) > 0) {
                    foreach ($this->folder->GetByIds($folderIds)->get() as $folder)
                    {
                        $folder->users()->sync($shareUserIds);
                    }
                }
                if (count($filesIds) > 0) {
                    foreach ($files as $file)
                    {
                       $file->users()->sync($shareUserIds);
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
        $file = $this->file->where('id', $folderIdOrFileId)->first();
        if (is_null($file))
        {
            throw new ValidationException('file không tồn tại !', 422);
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
    public function downloadFolderOrFile(TypeCheckFolderOrFileRequest $request , $folderIdOrFileId)
    {
        $typeCheck     = $request->input('type_check');
        if ($typeCheck == Status::FOLDER)
        {
            $currentFolder = $this->folder->GetById($folderIdOrFileId)->first();
            if (is_null($currentFolder)) {
                throw new ValidationException('Folder không tồn tại', 422);
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

//        download file

        $file = $this->file->findOrFail($folderIdOrFileId);
        if (is_null($file)) {
            throw new ValidationException('File không tồn tại', 422);
        }
       $path = Storage::path('public/files'.'/'.$file->name);
        if (file_exists($path))
        {
            return response()->download($path);
        }else
        {
            throw new ValidationException('error download file', 422);
        }
    }

    /**
     * @param  Folder  $folder
     * @param  RenameFolderRequest  $request
     * @return JsonResponse
     */

    public function renameFolder(Folder $folder , RenameFolderRequest $request)
    {
        $name = $request->input('name');
        $folder->name = $name;
        $folder->save();
        return $this->successResponse(null, 'oke', 201);

    }

    /**
     * @param  TypeCheckFolderOrFileRequest $request
     * @param $folderIdOrFileId
     * @return JsonResponse
     */

    public function movedFolderOrFile(TypeCheckFolderOrFileRequest $request , $folderIdOrFileId)
    {
        $typeCheck      = $request->input('type_check');
        $folderParentId = $request->input('folder_parent_id');

        if ($folderParentId) {
            $folderParentMoved = $this->folder->find($folderParentId);
            if (is_null($folderParentMoved)) {
                throw new ValidationException('Folder cần di đến chuyển không tồn tại !', 422);
            }
        }
        if ($typeCheck == Status::FOLDER) {
            $folderParent = $this->folder->GetByParent($folderParentId)->find($folderIdOrFileId);
            $folder       = $this->folder->find($folderIdOrFileId);
            if ( ! is_null($folderParent)) {
                throw new ValidationException('Folder đã ở vị trí cần chuyển !', 422);
            }
            if (is_null($folder)) {
                throw new ValidationException('Folder cần di chuyển không tồn tại !', 422);
            }

            $folder->parent_id = $folderParentId;
            $folder->save();
            return $this->successResponse(null, 'oke', 201);
        }

//        moved file

        $file = $this->file->where('id', $folderIdOrFileId)->first();
        if (is_null($file))
        {
            throw new ValidationException('file không tồn tại !', 422);
        }
        $file->folder_id = $folderParentId;
        $file->save();
        return $this->successResponse(null, 'oke', 201);
    }

    /**
     * @param  DeleteFolderOrFileRequest  $request
     * @return JsonResponse
     */

    public function deleteFolderOrFile(DeleteFolderOrFileRequest $request)
    {
        $type = $request->input('type' , TypeDelete::SOFT_DELETE);
        $folderFileId   = $request->input('folder_file_ids' , []);
        $folderFileId   = collect($folderFileId)->groupBy('type_check');
        $folderTypeId = data_get($folderFileId , Status::FOLDER ,[]);
        $fileTypeId =  data_get($folderFileId , Status::FILE , []);
        DB::beginTransaction();
        try {
            if (count($folderTypeId) > 0)
            {
                $folders = $this->folder->CheckTrashed($type)->GetByIds(collect($folderTypeId)->pluck('id'));
                $folders->each(function ($folder) use ($type) {
                    if ($type == TypeDelete::SOFT_DELETE)
                    {
                        $folder->delete();
                    }else
                    {
                        $folder->withTrashed()->forceDelete();
                    }
                });
            }

            if (count($fileTypeId) >0)
            {
                $files = $this->file->CheckTrashed($type)->GetByIds(collect($fileTypeId)->pluck('id'));
                $files->each(function ($file) use ($type) {
                    if ($type == TypeDelete::SOFT_DELETE)
                    {
                        $file->delete();
                    }else
                    {
                        $file->withTrashed()->forceDelete();
                    }
                });
            }
            DB::commit();
            return $this->successResponse(null, 'oke', 201);
        }catch (DomainException $exception)
        {
            DB::rollBack();
            return  $this->errorResponse('Lỗi delete folder' , 500);
        }

    }

    /**
     * @param  TypeExportDocumentRequest  $request
     * @return BinaryFileResponse
     * @throws BindingResolutionException
     */

    public function exportDocument(TypeExportDocumentRequest $request)
    {
        $extends       = $request->input('type', 'rar');
        $folders       = $this->folder->where('user_id', Auth::id())->get();
        $nameFolderZip = time().'-export.'.$extends;
        try {
            $zip     = new ZipArchive();
            $zipFile = app()->make(FolderServiceInterface::class);
            if ($zip->open(public_path($nameFolderZip), ZipArchive::CREATE) === true) {
                foreach ($folders as $folder) {
                    $path = $folder->name.'/';
                    $zipFile->zipToFileAndFolder($zip, $path, $folder);
                }
                $zip->close();
            }
            return response()->download($nameFolderZip)->deleteFileAfterSend();

        } catch (DomainException $exception) {
            throw new DomainException($exception->getMessage(), 500);
        }
    }


}
