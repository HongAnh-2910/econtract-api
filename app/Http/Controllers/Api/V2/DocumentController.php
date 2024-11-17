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

class DocumentController extends Controller
{

    const TYPE_FOLDER ='folder';
    const TYPE_FILE = 'file';

    public function index(Request $request)
    {
        $size = $request->input('size' ,15);
        $folderId = $request->input('folder_id');
        if ($folderId)
        {
            throw_if(!Folder::where('id' , $folderId)->first() ,new ModelNotFoundException('Không tồn tại folder' ,400));
           return  $this->getDocument($folderId , $folderId , $size);
        }
        return $this->getDocument(null, null , $size);
    }

    /**
     * @param $parentFolderId
     * @param $fileByFolderId
     * @param $size
     * @param $folderId
     * @return array
     */

    private function getDocument($parentFolderId = null, $fileByFolderId = null, $size ,  $folderId = null)
    {
        $folders =  Folder::with(['treeChildren' ,'files']);
        if ((is_null($parentFolderId) && is_null($folderId)) or (!empty($parentFolderId) && is_null($folderId)))
        {
            $folders->where('parent_id', $parentFolderId);
        }
        if (!empty($folderId))
        {
            $folders->where('id', $folderId);
        }
        $folders   = $folders->get();
        $files     = File::where('folder_id', $fileByFolderId)->get();
        $documents = array_merge($folders->toArray(), $files->toArray());
        $documents = collect($documents)->sortByDesc('created_at')->values()->map(function ($item) {
            $item['date'] = Carbon::parse($item['created_at'])->format('d-m-Y H:s:i');
            unset($item['created_at'], $item['updated_at']);
            return $item;
        });
        if ($size)
        {
            return $documents->customerPaginate($size);
        }

        return $documents->all();
    }

    public function moveDocument(Request $request)
    {
        $formId = $request->input('form_id');
        $toId = $request->input('to_id');
        $type = $request->input('type');

//        return $folders;
//        if ($type == self::TYPE_FOLDER)
//        {
//            return $document = $this->getDocument(null, $formId , null , $formId);
//        }
    }

    public function copyDocument(Request $request)
    {
        $formId = $request->input('form_id');
        $toId = $request->input('to_id');
        $type = $request->input('type');
        if ($type == self::TYPE_FOLDER)
        {
            if (Folder::where('name' , Folder::where('id' , $formId)->first()->name)->where('parent_id' , $toId)->first())
            {
                throw new \Exception('Folder đã tồn tại');
            }
            $folder =  Folder::with(['treeChildren'])->find($formId);
            $folderCreate = $this->createFolder($folder , $toId);
            $this->treeGetFolderId($folder['id'] , $folderCreate->id);
            return  Folder::with(['treeChildren'])->find($folderCreate->id);
        }
    }

    private function treeGetFolderId($folderIdOld , $folderIdNew)
    {
        $files = File::where('folder_id' , $folderIdOld)->get();
        foreach ($files as $file)
        {
            File::create([
                "name" => $file->name,
                "path" =>  $file->path,
                "type" =>  $file->type,
                "user_id" => Auth::id(),
                "folder_id" => $folderIdNew,
                "size"=> $file->size,
                "upload_st"=> $file->upload_st,
                "contract_id"=> $file->contract_id,
            ]);
        }
        $folders =  Folder::with(['treeChildren'])->where('parent_id', $folderIdOld)->get();
        if (!empty($folders))
        {
            foreach ($folders as $folder)
            {
                $folderCreate = $this->createFolder($folder, $folderIdNew);
                $this->treeGetFolderId($folder['id'] , $folderCreate->id);
            }
        }
    }

    private function createFolder($folder , $folderIdNew)
    {
        return Folder::create([
            "name"      => $folder->name,
            "user_id"   => Auth::id(),
            "parent_id" => $folderIdNew,
            "slug"      => $folder->slug,
        ]);
    }




    public function uploadFile()
    {

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
