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

                return Inertia::render('Crud/Grid', [
                    'urlFetch' => route('crud.grid.fetch', ['grid' => $grid]),
                    'urlForm' => \route('crud.grid.form', ['grid' => $grid, 'id' => ':id']),
                    'urlDelete' => \route('crud.grid.delete', ['grid' => $grid, 'id' => ':id']),
                    ...$gridProps,
                ]);
            })
                ->where('grid', '[\w]+')
                ->name('crud.grid');

            Route::get('/crud/{grid}/fetch', function (
                CrudAttributesService $attributesService,
                Request               $request,
                string                $grid,
            ) {
                $className = $attributesService->getClassName($grid);
                $columns = $attributesService->getColumns($grid) ?? [];
                if (empty($className)) {
                    throw new \Exception('not found'); // @todo
                }

                $page = ((int)$request->input('page') - 1);
                $rowsPerPage = ((int)$request->input('rowsPerPage'));
                $sortBy = $request->input('sortBy');
                $descending = $request->input('descending');

                /** @var \Illuminate\Database\Eloquent\Builder $select */
                $select = $className::orderBy($sortBy, $descending ? 'desc' : 'asc');
                $items = $rowsPerPage
                    ? $select->offset($page * $rowsPerPage)->limit($rowsPerPage)
                    : $select;

                $filter = $request->input('filter');
                $filter = json_decode($filter, true);
                $filter = array_filter($filter, function ($terms, $name) use ($columns) {
                    return @$columns[$name]?->isSearchable() && !empty($terms);
                }, ARRAY_FILTER_USE_BOTH);
                foreach ($filter as $field => $term) {
                    $term = trim($term);
                    $select->where($field, 'like', "%{$term}%");
                }

                $data = [
                    'rowsNumber' => $select->count(),
                    'items' => $items->get()->toArray(),
                ];
                return response()->json($data);
            })
                ->where('grid', '\w+')
                ->name('crud.grid.fetch');

            Route::delete('/crud/{grid}/delete/{id}', function (
                CrudAttributesService $attributesService,
                string $grid,
                int $id,
            ) {
                $className = $attributesService->getClassName($grid);
                if (empty($className)) {
                    throw new \Exception('not found'); // @todo
                }

                $className::destroy($id);
                return response()->json();
            })
                ->where('grid', '\w+')
                ->where('id', '\d+')
                ->name('crud.grid.delete');


            Route::get('/crud/{grid}/form/{id?}', function (
                CrudAttributesService $attributesService,
                string                $grid,
                ?int                  $id = null,
            ) {
                $formProps = $attributesService->getFormProps($grid);
                $className = $attributesService->getClassName($grid);
                if (!$formProps || !$className) {
                    throw new \Exception('not found'); // @todo
                }

                return Inertia::render('Crud/Form', [
                    'postUrl' => \route('crud.grid.form.post', ['grid' => $grid, 'id' => $id]),
                    'current' => $className::find($id)?->toArray() ?? [],
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
                $className = $attributesService->getClassName($grid);
                $fields = $attributesService->getFields($grid);
                if (empty($className) || empty($fields)) {
                    throw new \Exception('not found'); // @todo
                }

                $fields = array_map(fn($field) => $field->getName(), $fields);
                $fields = array_map(
                    fn($fieldName) => ['key' => $fieldName, 'value' => $request->input($fieldName)],
                    $fields,
                );
                $fields = array_column($fields, 'value', 'key');
                $model = $className::findOr($id, fn() => new $className());
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
