<?php

namespace App\Http\Controllers;

use App\Http\Requests\CrudRequest;
use App\Providers\CrudAttributesService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class Crud extends Controller
{
    public function __construct(
        protected readonly CrudAttributesService $attributesService,
        protected readonly Request $request,
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

    public function grid()
    {
        $gridProps = $this->attributesService->getGridProps();
        if (!$gridProps) {
            throw new \Exception('not found'); // @todo
        }

        return Inertia::render('Crud/Grid', [
            'urlFetch' => route('crud.grid.fetch', ['grid' => $this->attributesService->grid]),
            'urlForm' => \route('crud.grid.form', ['grid' => $this->attributesService->grid, 'id' => ':id']),
            'urlDelete' => \route('crud.grid.delete', ['grid' => $this->attributesService->grid, 'id' => ':id']),
            ...$gridProps,
        ]);
    }

    public function gridFetch()
    {
        $className = $this->attributesService->getClassName();
        $columns = $this->attributesService->getColumns() ?? [];
        if (empty($className)) {
            throw new \Exception('not found'); // @todo
        }

        $page = ((int)$this->request->input('page') - 1);
        $rowsPerPage = ((int)$this->request->input('rowsPerPage'));
        $sortBy = $this->request->input('sortBy');
        $descending = $this->request->input('descending');

        /** @var \Illuminate\Database\Eloquent\Builder $select */
        $select = $className::orderBy($sortBy, $descending ? 'desc' : 'asc');
        $items = $rowsPerPage
            ? $select->offset($page * $rowsPerPage)->limit($rowsPerPage)
            : $select;

        $filter = $this->request->input('filter');
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

    public function deleteRecord()
    {
        $className = $this->attributesService->getClassName();
        if (empty($className)) {
            throw new \Exception('not found'); // @todo
        }

        $id = $this->request->route('id');
        $className::destroy($id);
        return response()->json();
    }

    public function form()
    {
        $formProps = $this->attributesService->getFormProps();
        $className = $this->attributesService->getClassName();
        if (!$formProps || !$className) {
            throw new \Exception('not found'); // @todo
        }

        $id = $this->request->route('id');
        $params = [
            'postUrl' => \route('crud.grid.form.post', ['grid' => $this->attributesService->grid, 'id' => $id]),
            'current' => ($className::find($id) ?? new $className())->toArray(),
            ...$formProps,
        ];
        return Inertia::render('Crud/Form', $params);
    }

    public function save(CrudRequest $request)
    {
        $className = $this->attributesService->getClassName();
        $fields = $this->attributesService->getFields();
        if (empty($className) || empty($fields)) {
            throw new \Exception('not found'); // @todo
        }

        $fields = array_map(
            fn($field) => ['key' => $field->getName(), 'value' => $field->decode($request->safe()->offsetGet($field->getName()))],
            $fields,
        );
        $fields = array_column($fields, 'value', 'key');
        $model = $className::findOr($this->request->route('id'), fn() => new $className());
        $model->fill($fields);
        $model->team_id = $this->request->user()->currentTeam->id;
        $model->save();

        return redirect()->route('crud.grid', ['grid' => $this->attributesService->grid]);
    }
}
