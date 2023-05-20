<?php

namespace App\Http\Controllers;

use App\Providers\CrudAttributesService;
use Illuminate\Http\Request;
use Inertia\Inertia;

/**
 * @property-read string $grid
 */
class Crud extends Controller
{
    public function __construct(
        protected readonly CrudAttributesService $attributesService,
    ) {
        $authMiddleware = config('jetstream.guard')
            ? 'auth:' . config('jetstream.guard')
            : 'auth';

        $authSessionMiddleware = config('jetstream.auth_session', false)
            ? config('jetstream.auth_session')
            : null;

        $this->middleware('verified');
        $this->middleware($authMiddleware);
        $this->middleware($authSessionMiddleware);
    }

    public function list()
    {
        $listProps = $this->attributesService->getListProps();
        return Inertia::render('Crud/List', $listProps);
    }

    public function grid(string $grid)
    {
        $gridProps = $this->attributesService->getGridProps($grid);
        if (!$gridProps) {
            throw new \Exception('not found'); // @todo
        }

        return Inertia::render('Crud/Grid', [
            'urlFetch' => route('crud.grid.fetch', ['grid' => $grid]),
            'urlForm' => \route('crud.grid.form', ['grid' => $grid, 'id' => ':id']),
            'urlDelete' => \route('crud.grid.delete', ['grid' => $grid, 'id' => ':id']),
            ...$gridProps,
        ]);
    }

    public function gridFetch(Request $request, string $grid)
    {
        $className = $this->attributesService->getClassName($grid);
        $columns = $this->attributesService->getColumns($grid) ?? [];
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
    }

    public function deleteRecord(string $grid, int $id)
    {
        $className = $this->attributesService->getClassName($grid);
        if (empty($className)) {
            throw new \Exception('not found'); // @todo
        }

        $className::destroy($id);
        return response()->json();
    }

    public function form(string $grid, ?int $id = null)
    {
        $formProps = $this->attributesService->getFormProps($grid);
        $className = $this->attributesService->getClassName($grid);
        if (!$formProps || !$className) {
            throw new \Exception('not found'); // @todo
        }

        return Inertia::render('Crud/Form', [
            'postUrl' => \route('crud.grid.form.post', ['grid' => $grid, 'id' => $id]),
            'current' => $className::find($id)?->toArray() ?? [],
            ...$formProps,
        ]);
    }

    public function save(Request $request, string $grid, ?int $id)
    {
        $className = $this->attributesService->getClassName($grid);
        $fields = $this->attributesService->getFields($grid);
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
    }
}
