<?php

namespace App\Providers;

use App\Models\Menu;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        View::addNamespace('admin', resource_path('admin/views'));

        if (!app()->runningInConsole()) {
            $rootUrl = rtrim(Request::root(), '/');

            URL::forceRootUrl($rootUrl);
            config([
                'app.url' => $rootUrl,
                'filesystems.disks.public.url' => $rootUrl . '/storage',
                'filesystems.disks.uploads.url' => $rootUrl . '/uploads',
            ]);
        }

        if (!app()->runningInConsole() && Request::is('admin/*') && Schema::hasTable('menus')) {
            $menu = Menu::with('children')->whereNull('disabled_at')->whereNull('parent_id')->orderBy('ordering')->get();
            view()->share('menu', $menu);
        }
    }
}
