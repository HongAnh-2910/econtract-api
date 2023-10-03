<?php

namespace App\Providers;

use App\Services\FileService\FileService;
use App\Services\FileService\FileServiceInterface;
use App\Services\FolderService\FolderService;
use App\Services\FolderService\FolderServiceInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Collection;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(FolderServiceInterface::class , FolderService::class);
        $this->app->singleton(FileServiceInterface::class , FileService::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Model::preventLazyLoading();
    }
}
