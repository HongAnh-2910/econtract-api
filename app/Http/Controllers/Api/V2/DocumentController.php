<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\Folder\FolderResource;
use App\Models\File;
use App\Models\Folder;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentController extends Controller
{

    const TYPE_FOLDER = 'folder';
    const TYPE_FILE = 'file';

    public function index(Request $request)
    {
        $size = $request->input('size', 15);
        $folderId = $request->input('folder_id');
        if ($folderId) {
            throw_if(!Folder::where('id', $folderId)->first(), new ModelNotFoundException('Không tồn tại folder', 400));
            return $this->getDocument($folderId, $folderId, $size);
        }
<<<<<<< HEAD
=======
        dd(1);
>>>>>>> 123a
        return $this->getDocument(null, null, $size);
    }

    /**
     * @param $parentFolderId
     * @param $fileByFolderId
     * @param $size
     * @param $folderId
     * @return array
     */

    private function getDocument($parentFolderId = null, $fileByFolderId = null, $size, $folderId = null)
    {
        $folders = Folder::with(['treeChildren', 'files']);
<<<<<<< HEAD
        if (!empty($folderId)) {
            $folders->where('id', $folderId);
        } else {
            $folders->where('parent_id', $parentFolderId);
        }
=======
        if ((is_null($parentFolderId) && is_null($folderId)) or (!empty($parentFolderId) && is_null($folderId))) {
            $folders->where('parent_id', $parentFolderId);
        }
        if (!empty($folderId)) {
            $folders->where('id', $folderId);
        }
>>>>>>> 123a
        $folders = $folders->get();
        $files = File::where('folder_id', $fileByFolderId)->get();
        $documents = array_merge($folders->toArray(), $files->toArray());
        $documents = collect($documents)->sortByDesc('created_at')->values()->map(function ($item) {
            $item['date'] = Carbon::parse($item['created_at'])->format('d-m-Y H:s:i');
            unset($item['created_at'], $item['updated_at']);
            return $item;
        });
        if ($size) {
            return $documents->customerPaginate($size);
        }

        return $documents->all();
    }

    public function moveDocument(Request $request)
    {
        $form = $request->input('from', []);
        $toFolderId = $request->input('to_folder_id');
        $files = File::whereIn('id', data_get($form, 'file_ids', []));
        if (!empty(data_get($form, 'folder_ids'))) {
            $folders = Folder::whereIn('id', data_get($form, 'folder_ids'))->get();
            $this->validateCopyAndMoveFolder($form, $toFolderId, $folders);
            $this->validateCopyAndMoveFile($files, $toFolderId);
            $folders->each(function ($item) use ($toFolderId) {
                $item->update([
                    'parent_id' => $toFolderId,
                ]);
            });
            $files->each(function ($item) use ($toFolderId) {
                $item->update([
                    'folder_id' => $toFolderId
                ]);
            });
            return true;
        }

<<<<<<< HEAD
        $files = File::whereIn('id', data_get($form, 'file_ids'));
        $this->validateCopyAndMoveFile($files, $toFolderId);
        $files->each(function ($item) use ($toFolderId) {
            $item->update([
                'folder_id' => $toFolderId
            ]);
        });
        return true;
=======
        //        return $folders;
//        if ($type == self::TYPE_FOLDER)
//        {
//            return $document = $this->getDocument(null, $formId , null , $formId);
//        }
>>>>>>> 123a
    }

    public function copyDocument(Request $request)
    {
<<<<<<< HEAD
        $form = $request->input('from', []);
        $toFolderId = $request->input('to_folder_id');
        $files = File::whereIn('id', data_get($form, 'file_ids'));
        $this->validateCopyAndMoveFile($files, $toFolderId);
        if (!empty(data_get($form, 'folder_ids'))) {
            $folders = Folder::whereIn('id', data_get($form, 'folder_ids'))->get();
            $this->validateCopyAndMoveFolder($form, $toFolderId, $folders);
            $folders = $folders->load(['treeChildren', 'files']);
            foreach ($folders as $folder) {
                $folderCreate = $this->createFolder($folder, $toFolderId);
                $this->treeGetFolderId($folder, $folder->id, $folderCreate->id);
            }
            foreach ($files->get() as $file) {
                $this->createFile($file, $toFolderId);
            }
            return Folder::with(['treeChildren', 'files'])
                ->where('id', $toFolderId)
                ->first();
=======
        $formId = $request->input('form_id');
        $toId = $request->input('to_id');
        $type = $request->input('type');
        if ($type == self::TYPE_FOLDER) {
            if (Folder::where('name', Folder::where('id', $formId)->first()->name)->where('parent_id', $toId)->first()) {
                throw new \Exception('Folder đã tồn tại');
            }
            $folder = Folder::with(['treeChildren'])->find($formId);
            $folderCreate = $this->createFolder($folder, $toId);
            $this->treeGetFolderId($folder['id'], $folderCreate->id);
            return Folder::with(['treeChildren'])->find($folderCreate->id);
>>>>>>> 123a
        }
        foreach ($files->get() as $file) {
            $this->createFile($file, $toFolderId);
        }
        return File::where('folder_id', $toFolderId)->get();

    }

<<<<<<< HEAD


    private function treeGetFolderId($folder, $id, $idFolder)
    {
        if (!empty($files = $folder->files)) {
            foreach ($files as $file) {
                $this->createFile($file, $idFolder);
=======
    private function treeGetFolderId($folderIdOld, $folderIdNew)
    {
        $files = File::where('folder_id', $folderIdOld)->get();
        foreach ($files as $file) {
            File::create([
                "name" => $file->name,
                "path" => $file->path,
                "type" => $file->type,
                "user_id" => Auth::id(),
                "folder_id" => $folderIdNew,
                "size" => $file->size,
                "upload_st" => $file->upload_st,
                "contract_id" => $file->contract_id,
            ]);
        }
        $folders = Folder::with(['treeChildren'])->where('parent_id', $folderIdOld)->get();
        if (!empty($folders)) {
            foreach ($folders as $folder) {
                $folderCreate = $this->createFolder($folder, $folderIdNew);
                $this->treeGetFolderId($folder['id'], $folderCreate->id);
>>>>>>> 123a
            }
        }

        if (!empty($folder->treeChildren)) {
            foreach ($folder->treeChildren as $child) {
                if ($child->parent_id == $id) {
                    $folderCreate = $this->createFolder($child, $idFolder);
                    $this->treeGetFolderId($child, $child->id, $folderCreate->id);

                }
            }
        }
        return true;
    }

<<<<<<< HEAD
    private function createFile($file, $folderIdNew)
    {
        return File::create([
            "name" => $file->name,
            "path" => $file->path,
            "type" => $file->type,
            "user_id" => Auth::id(),
            "folder_id" => $folderIdNew,
            "size" => $file->size,
            "upload_st" => $file->upload_st,
            "contract_id" => $file->contract_id,
=======
    private function createFolder($folder, $folderIdNew)
    {
        return Folder::create([
            "name" => $folder->name,
            "user_id" => Auth::id(),
            "parent_id" => $folderIdNew,
            "slug" => $folder->slug,
>>>>>>> 123a
        ]);
    }

    private function createFolder($folder, $folderIdNew)
    {
        dd('12300');
        return Folder::create([
            "name" => $folder->name,
            "user_id" => Auth::id(),
            "parent_id" => $folderIdNew,
            "slug" => $folder->slug,
        ]);
    }

    private function validateCopyAndMoveFolder($form, $toFolderId, $folders)
    {
        if ((count($folders) != count(data_get($form, 'folder_ids')))) {
            throw new \Exception('Truyền id folder không đúng');
        }
        $nameFolder = $folders->pluck('name')->toArray();
        if (Folder::whereIn('name', $nameFolder)->where('parent_id', $toFolderId)->count() > 0) {
            throw new \Exception('Folder đã tồn tại trong thư mục cần copy đến');
        }
        $folderIdFirst = data_get($form, 'folder_ids.0');
        $getFolderFirstParentId = Folder::where('id', $folderIdFirst)->first()->parent_id;
        $checkFolderSameFolderParent = $folders->every(function ($item) use ($getFolderFirstParentId) {
            return $item->parent_id == $getFolderFirstParentId;
        });
        if (!$checkFolderSameFolderParent) {
            throw new \Exception('Lấy id folder không cùng folder cha');
        }
    }

    private function validateCopyAndMoveFile($file, $toFolderId)
    {
        $getNameFiles = $file->pluck('name')->toArray();
        $checkFile = File::where('name', $getNameFiles)->where('folder_id', $toFolderId)->count();
        throw_if($checkFile > 0, new \DomainException('File đã tồn tại trong thư mục cần copy đến', 400));
    }




    public function uploadFile(Request $request)
    {
        $files = $request->file('files');
        $folderId = $request->input('folder_id');
        $userShareIds = $request->input('user_share_ids', []);
        if ($request->hasFile('files')) {
            DB::beginTransaction();
            try {
                $arr = [];
                $nameFile = [];
                foreach ($files as $file) {
                    $name = time() . '-' . $file->getClientOriginalName();
                    $nameFile[] = $name;
                    $storage = floor((int) $file->getSize() / 1024);
                    $extension = $file->getClientOriginalExtension();
                    $arr[] = [
                        'name' => $name,
                        'path' => $name,
                        'type' => $extension,
                        'user_id' => Auth::id(),
                        'folder_id' => $folderId,
                        'size' => $storage
                    ];
                    handleUploadFile($file, Storage::path('public/files'), $name);
                }
                File::insert($arr);
                $getFilesInsert = File::whereIn('name', $nameFile)->get();
                $getFilesInsert->each(function ($item) use ($userShareIds) {
                    return $item->users()->attach($userShareIds);
                });
                DB::commit();
                return $this->successResponse(null, 'File upload success', 200);
            } catch (\Exception $exception) {
                DB::rollBack();
                if (!empty($nameFile)) {
                    foreach ($nameFile as $name) {
                        handleRemoveFile(Storage::path('public/files'), $name);
                    }
                }
            }
        }
        throw new \DomainException('File upload error');
    }

    public function shareDocument(Request $request)
    {
        $folderIds = $request->input('folder_ids', []);
        $fileIds = $request->input('file_ids', []);
        $userShareIds = $request->input('user_share_ids', []);
        $folders = Folder::whereIn('id', $folderIds);
        $files = File::whereIn('id', $fileIds);
        throw_if($folders->count() != count($folderIds), new \DomainException('Id folder truyền vào không chính xác', 400));
        throw_if($files->count() != count($fileIds), new \DomainException('Id file truyền vào không chính xác', 400));
        throw_if($files->count() != count($fileIds), new \DomainException('Id file truyền vào không chính xác', 400));
        DB::beginTransaction();
        try {
            $folders->each(function ($item) use ($userShareIds) {
                return $item->users()->sync($userShareIds);
            });

            $files->each(function ($item) use ($userShareIds) {
                return $item->users()->sync($userShareIds);
            });
            return $this->successResponse(null, 'success', 200);

        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->errorResponse(null, 'error');
        }
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $folderId = $request->input('folder_id');
        $name = $request->input('name');
        $folder = Folder::create([
            "name" => $name,
            "user_id" => Auth::id(),
            "parent_id" => $folderId,
            "slug" => Str::slug($name),
        ]);
        return $folder;
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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
