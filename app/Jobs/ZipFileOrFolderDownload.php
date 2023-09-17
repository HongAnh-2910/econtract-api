<?php

namespace App\Jobs;

use App\Models\Folder;
use App\Services\FolderService\FolderServiceInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use ZipArchive;

class ZipFileOrFolderDownload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $nameFileZip;
    protected Folder $folder;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $nameFileZip , Folder $folder)
    {
        $this->nameFileZip = $nameFileZip;
        $this->folder = $folder;
    }

    /**
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function handle()
    {
        $zip           = new ZipArchive();
        $nameFolderZip = $this->nameFileZip;
        $currentFolder = $this->folder;
        $path    = '';
        $zipFile = app()->make(FolderServiceInterface::class);
        if ($zip->open(public_path($nameFolderZip), ZipArchive::CREATE) === true) {
            $zipFile->zipToFileAndFolder($zip, $path, $currentFolder);
            $zip->close();
        }
    }
}
