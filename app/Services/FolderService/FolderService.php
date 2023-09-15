<?php

    namespace App\Services\FolderService;

    use App\Models\File;
    use Illuminate\Support\Facades\Storage;

    class FolderService implements  FolderServiceInterface
    {
        /**
         * @var File
         */
        protected File $file;

        public function __construct(File $file)
        {
            $this->file = $file;
        }

        /**
         * @param  \ZipArchive  $zip
         * @param $path
         * @param $folder
         * @return mixed|void
         */

        public function zipToFileAndFolder(\ZipArchive $zip , $path ,  $folder)
        {
            $filesByFolder = $this->file->where('folder_id' , $folder->id)->get();
            $childFolders = $folder->children()->get();

            if (count($filesByFolder)) {
                foreach ($filesByFolder as $item) {
                    if (file_exists($fileName = Storage::path('public/files').'/'.$item->name)) {
                        $zip->addFile($fileName, $path . $item->name);
                    }
                }
            } else {
                $zip->addEmptyDir($path);
            }

            if (count($childFolders)) {
                foreach ($childFolders as $childFolder) {
                    $this->zipToFileAndFolder($zip, $path . $childFolder->name . '/', $childFolder);
                }
            }

            if (!$zip->numFiles) {
                $zip->addEmptyDir(strtolower($folder->name));
            }
        }
    }
