<?php

    namespace App\Services\FileService;

    use App\Models\File;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Storage;

    class FileService implements FileServiceInterface
    {
        private File $file;

        public function __construct(File $file)
        {
            $this->file = $file;
        }

        public function uploadMultipleFileAndCreateDatabase(Request $request, string $uploadFileSt)
        {
            $fileIds   = [];
            $nameFiles = [];
            DB::beginTransaction();
            try {
                if ($request->hasFile('files')) {
                    foreach ($request->file('files') as $file) {
                        $name        = time().'-'.$file->getClientOriginalName();
                        $nameFiles[] = $name;
                        $storage     = floor((int)$file->getSize() / 1024);
                        $extension   = $file->getClientOriginalExtension();
                        handleUploadFile($file, Storage::path('public/files'), $name);
                        $fileInstance = $this->file->create([
                            'name'      => $name,
                            'path'      => $name,
                            'type'      => $extension,
                            'user_id'   => Auth::id(),
                            'folder_id' => null,
                            'size'      => $storage,
                            'upload_st' => $uploadFileSt
                        ]);
                        $fileIds[]    = $fileInstance->id;
                    }
                }
                DB::commit();
                return [
                    'fileIds'   => $fileIds,
                    'nameFiles' => $nameFiles
                ];
            } catch (\Exception $exception) {
                DB::rollBack();
                throw new \Exception($exception->getMessage());
            }

        }
    }
