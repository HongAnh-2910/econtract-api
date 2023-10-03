<?php

    namespace App\Services\FileService;

    use Illuminate\Http\Request;

    interface FileServiceInterface
    {
        public function uploadMultipleFileAndCreateDatabase(Request $request , string $uploadFileSt);
    }
