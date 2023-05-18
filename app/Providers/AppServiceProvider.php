<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use Laravel\Jetstream\Jetstream;
use App\Attributes\Grid;
use App\Attributes\Form;

class AppServiceProvider extends ServiceProvider
{
    protected array $grid = [];

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
        Jetstream::role('reader', 'Reader', [
            'read',
        ])->description('Reader users have the ability to read.');


        $this->eloquentCrudModels();
    }

    final protected function eloquentCrudModels()
    {
        $eloquentCrudModels = config('app.eloquent_crud', []);
        foreach ($eloquentCrudModels as $eloquentCrudModel) {
            $reflectionClass = new \ReflectionClass($eloquentCrudModel);

            $paginatorAttribute = $reflectionClass->getAttributes(Grid\Paginator::class);
            $paginatorAttribute = array_shift($paginatorAttribute)?->newInstance();
            if (!$paginatorAttribute instanceof Grid\Paginator) {
                continue;
            }

            $this->grid[$paginatorAttribute->getPath() ?? $reflectionClass->getShortName()] = $eloquentCrudModel;
        }

        if (empty($this->grid)) {
            return;
        }

        $authMiddleware = config('jetstream.guard')
            ? 'auth:'.config('jetstream.guard')
            : 'auth';

        $authSessionMiddleware = config('jetstream.auth_session', false)
            ? config('jetstream.auth_session')
            : null;

        Route::middleware(array_filter([
            'web',
            $authMiddleware,
            $authSessionMiddleware,
            'verified',
        ]))->group(function () {

            Route::get('/crud', function () {
                return Inertia::render('Crud/List', [
                    'grids' => array_keys($this->grid)
                ]);
            })->name('crud');

            Route::get('/crud/{grid}', function (string $grid) {
                if (!array_key_exists($grid, $this->grid)) {
                    throw new \Exception('not found'); // @todo
                }

                $reflectionClass = new \ReflectionClass($this->grid[$grid]);
                $paginatorAttribute = $reflectionClass->getAttributes(Grid\Paginator::class);
                $paginatorAttribute = array_shift($paginatorAttribute)?->newInstance();

                if (!$paginatorAttribute instanceof Grid\Paginator) {
                    throw new \Exception('no paginator'); // @todo
                }

                $columnAttributes = $reflectionClass->getAttributes(Grid\Column::class);
                $columnAttributes = array_map(function ($columnAttribute) {
                    if ($columnAttribute instanceof Grid\Column) {
                        return $columnAttribute->toComponent();
                    }

                    return null;
                }, $columnAttributes);
                $columnAttributes = array_filter($columnAttributes);

                return Inertia::render('Crud/Grid', [
                    'size' => $paginatorAttribute->getSize(),
                    'columns' => $columnAttributes,
                ]);
            })
                ->where('grid', '[\w]+')
                ->name('crud.grid');
        });
    }
}
