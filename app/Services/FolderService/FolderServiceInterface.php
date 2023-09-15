<?php

    namespace App\Services\FolderService;

    use ZipArchive;

    interface FolderServiceInterface
    {
        /**
         * @param  ZipArchive  $zip
         * @param $path
         * @param $folder
         * @return mixed
         */

        public function zipToFileAndFolder(ZipArchive $zip , $path ,  $folder);
    }
