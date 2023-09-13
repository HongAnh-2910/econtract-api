<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

if (!function_exists('dataTree'))
{
    function dataTree($data, $id, $level = 0)
    {
        $result = collect([]);
        foreach ($data as $item) {
            if ($item->parent_id == $id) {
                $item['level'] = $level;
                $result->push($item);
                $child = dataTree($item->childrenDepartment, $item->id, $level + 1);
                $result = $result->merge($child);
            }
            unset($item);
        }

        return $result;
    }
}

if (!file_exists('handleUploadFile'))
{
    function handleUploadFile(UploadedFile $file , $disk, $nameFile):void
    {
            $file->move($disk, $nameFile);
    }
}

if (!file_exists('handleRemoveFile'))
{
    function handleRemoveFile($disk ,$nameFile):void
    {
         unlink($disk.'/'.$nameFile);
    }
}
