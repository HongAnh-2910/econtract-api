<?php

namespace App\Providers;

use App\Services\FolderService\FolderService;
use App\Services\FolderService\FolderServiceInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
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
        $this->app->bind(FolderServiceInterface::class , FolderService::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Collection::macro('customerPaginate', function ($perPage =15 , $page = null , $options = []) {
            $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
            $items = $this instanceof Collection ? $this : Collection::make($this);
            return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
        });
        Model::preventLazyLoading();
    }
}
