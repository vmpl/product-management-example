<?php

namespace App\Providers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use Laravel\Jetstream\Jetstream;

class AppServiceProvider extends ServiceProvider
{
    protected array $grid = [];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CrudAttributesService::class, function (Application $app) {
            return CrudAttributesService::init(
                $app['config']->get('app.eloquent_crud') ?? []
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


        $this->eloquentCrudModels();
    }

    final protected function eloquentCrudModels()
    {
        $authMiddleware = config('jetstream.guard')
            ? 'auth:' . config('jetstream.guard')
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

            Route::get('/crud', function (CrudAttributesService $attributesService) {
                $listProps = $attributesService->getListProps();
                return Inertia::render('Crud/List', $listProps);
            })->name('crud');

            Route::get('/crud/{grid}', function (CrudAttributesService $attributesService, string $grid) {
                $gridProps = $attributesService->getGridProps($grid);
                if (!$gridProps) {
                    throw new \Exception('not found'); // @todo
                }

                return Inertia::render('Crud/Grid', $gridProps);
            })
                ->where('grid', '[\w]+')
                ->name('crud.grid');


            Route::get('/crud/{grid}/form/{id?}', function (
                CrudAttributesService $attributesService,
                string                $grid,
                ?int                  $id = null,
            ) {
                $formProps = $attributesService->getFormProps($grid);
                if (!$formProps) {
                    throw new \Exception('not found'); // @todo
                }

                return Inertia::render('Crud/Form', [
                    'postUrl' => \route('crud.grid.form.post', ['grid' => $grid, 'id' => $id]),
                    ...$formProps,
                ]);
            })
                ->name('crud.grid.form')
                ->where('grid', '\w+')
                ->where('id', '\d+');

            Route::post('/crud/{grid}/form/{id?}', function (
                CrudAttributesService $attributesService,
                Request               $request,
                string                $grid,
                ?int                  $id = null,
            ) {
                $fields = $attributesService->getFieldNames($grid);
                $fields = array_map(
                    fn($fieldName) => ['key' => $fieldName, 'value' => $request->input($fieldName)],
                    $fields,
                );
                $fields = array_column($fields, 'value', 'key');
                $model = $this->grid[$grid]::findOr($id, fn() => new $this->grid[$grid]);
                $model->fill($fields);
                $model->team_id = $request->user()->currentTeam->id;
                $model->save();

                return redirect()->route('crud.grid', ['grid' => $grid]);
            })
                ->name('crud.grid.form.post')
                ->where('grid', '\w+')
                ->where('id', '\d+');
        });
    }
}
