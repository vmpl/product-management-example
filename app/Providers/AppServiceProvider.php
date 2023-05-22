<?php

namespace App\Providers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Laravel\Jetstream\Jetstream;

class AppServiceProvider extends ServiceProvider
{
    protected array $grid = [];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind('app.crud.grid', function (Application $application) {
            /** @var \Illuminate\Http\Request $request */
            $request = $application->make(\Illuminate\Http\Request::class);
            return $request->route('grid');
        });
        $this->app->singleton(CrudAttributesService::class, function (Application $app) {
            return CrudAttributesService::init(
                $app['config']->get('app.eloquent_crud') ?? [],
                $app->get('app.crud.grid')
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Jetstream::role('reader', 'Reader', [
            'read',
        ])->description('Reader users have the ability to read.');
    }
}
