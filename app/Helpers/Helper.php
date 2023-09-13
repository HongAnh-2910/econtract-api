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

if (!file_exists('checkImgTypeFile'))
{
    /**
     * @param $type
     * @return string
     */
    function checkExtensionFileGetImgType($type):string
    {
        $imgType = "";
//        jpeg,png,jpg,gif,pdf,doc,docx,xls,xlsx
        switch ($type) {
            case 'docx':case 'doc':
                $imgType = asset(config('pathUploadFile.path_svg_file').'/word.svg');
            break;
            case 'xls':case 'xlsx':
                $imgType = asset(config('pathUploadFile.path_svg_file').'/excel.svg');
                break;
            case 'jpeg':case 'png':case 'jpg':case 'gif':
                $imgType = asset(config('pathUploadFile.path_svg_file').'/image_thumb.svg');
                break;
            case 'pdf':
                $imgType = asset(config('pathUploadFile.path_svg_file').'/pdf.svg');
                break;
            case 'folder':
                $imgType = asset(config('pathUploadFile.path_svg_file').'/group_folder.svg');
                break;
            case 'file':
                $imgType = asset(config('pathUploadFile.path_svg_file').'/file.svg');
                break;
        }
        return $imgType;

    }
}
