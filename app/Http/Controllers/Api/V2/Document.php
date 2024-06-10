<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\Folder\FolderResource;
use App\Models\File;
use App\Models\Folder;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class Document extends Controller
{

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

    private function getDocument($folderId = null , $fileByFolderId = null , $size)
    {
        $folders = Folder::where('parent_id', $folderId)->with(['treeChildren'])->get();
        $files = File::where('folder_id', $fileByFolderId)->get();
        $documents = array_merge( $folders->toArray() ,$files->toArray());
        return collect($documents)->sortByDesc('created_at')->values()->map(function ($item){
            $item['date'] = Carbon::parse($item['created_at'])->format('d-m-Y H:s:i');
            unset($item['created_at'] , $item['updated_at']);
            return $item;
        })->customerPaginate($size);
    }

    public function moveDocument()
    {
        
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
