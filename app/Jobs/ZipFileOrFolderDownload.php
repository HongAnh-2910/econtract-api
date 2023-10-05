<?php

namespace App\Jobs;

use App\Exports\Application\ApplicationsExport;
use App\Models\Application;
use App\Models\Folder;
use App\Services\FolderService\FolderServiceInterface;
use Bschmitt\Amqp\Facades\Amqp;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use ZipArchive;

class ZipFileOrFolderDownload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels ,Batchable;
    protected $applications;
    public function __construct($applications)
    {
        $this->applications = $applications;
    }
    public function handle()
    {
        (new ApplicationsExport($this->applications))->store('public/export/application.xlsx');
    }
}
