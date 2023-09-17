<?php

namespace App\Console\Commands;

use App\Models\Folder;
use App\Services\FolderService\FolderServiceInterface;
use Dotenv\Exception\ValidationException;
use Illuminate\Console\Command;
use ZipArchive;

class FolderOrFileDownload extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:folder-file-download';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    protected $folder;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Folder $folder)
    {
        parent::__construct();
        $this->folder = $folder;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $folderId = 2;
        $currentFolder = $this->folder->where('id', $folderId)->first();
        if (is_null($currentFolder)) {
            throw new ValidationException('Folder không tồn tại', 500);
        }
        $nameFolderZip = time().'-'.$currentFolder->name.'.zip';
        $zip           = new ZipArchive();
        $path    = '';
        $zipFile = app()->make(FolderServiceInterface::class);
        if ($zip->open(public_path($nameFolderZip), ZipArchive::CREATE) === true) {
            $zipFile->zipToFileAndFolder($zip, $path, $currentFolder);
            $zip->close();
        }
        response()->download($nameFolderZip)->deleteFileAfterSend(true);
    }
}
