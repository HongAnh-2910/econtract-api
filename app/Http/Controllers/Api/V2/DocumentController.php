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
use Illuminate\Support\Facades\DB;

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
     * @param $folderId
     * @param $fileByFolderId
     * @param $size
     * @return mixed
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
            $folders =  Folder::with(['treeChildren'])->where('id', $formId)->get();
            $arrID = [];
            foreach ($folders as $folder)
            {
                $this->treeGetFolderId($folder['id'] , $arrID);
                array_push($arrID , $folder);
            }
            $ids = collect($arrID)->pluck('id');
            $data =Folder::whereIn('parent_id', $ids)->get();
            $data = array_merge([Folder::where('id' , $formId)->first()->toArray()] ,$data->toArray());
            $data = collect($data)->groupBy('parent_id');
            foreach ($data as $key => $value)
            {
                if ($key == "")
                {
//                    $folder = Folder::create([
//                        "name"      => $value[0]['name'],
//                        "user_id"   => Auth::id(),
//                        "parent_id" => $toId,
//                        "slug"      => $value[0]['slug'],
//                    ]);
//                    if (!empty(Folder::where('parent_id' , $value[0]['id'])->get()))
//                    {
//                        foreach (Folder::where('parent_id' , $value[0]['id'])->get() as $key1 => $value1)
//                        {
//                             Folder::create([
//                                "name"      => $value1['name'],
//                                "user_id"   => Auth::id(),
//                                "parent_id" => $folder['id'],
//                                "slug"      => $value1['slug'],
//                            ]);
//                        }
//                    }

                }else
                {
                    foreach ($value as $key => $value1)
                    {
                        $folder = Folder::create([
                        "name"      => $value[0]['name'],
                        "user_id"   => Auth::id(),
                        "parent_id" => $toId,
                        "slug"      => $value[0]['slug'],
                    ]);
                    }
                }
            }
//            DB::beginTransaction();
//            try {
//                collect($data)->each(function ($item , $key) {
//                    if ($key == 0)
//                    {
//                        dd($item);
//                    }
//                });
//
//                DB::commit();
//            }catch (\Exception $exception)
//            {
//                DB::rollBack();
//
//            }
//            $folderIds = collect($arrID)->pluck('id');
//            $files = File::whereIn('folder_id', $folderIds)->get();
//            return $folders;

        }
    }

    private function treeGetFolderId($id , &$arrId)
    {
        $folders =  Folder::with(['treeChildren'])->where('parent_id', $id)->get();
        if (!empty($folders))
        {
            foreach ($folders as $folder)
            {
                $this->treeGetFolderId($folder['id'] , $arrId);
                array_push($arrId , $folder);
            }
        }

        return $arrId;

//        if ($id)
//        {
//            $document = $this->getDocument(null, $id , null , $id);
//           array_push($arrId , $id);
//            foreach ($document as $key => $value)
//            {
//                $this->treeGetFolderId($id , $arrId);
//            }
//        }
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
