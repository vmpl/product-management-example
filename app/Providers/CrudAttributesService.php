<?php declare(strict_types=1);

namespace App\Providers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;
use App\Attributes\Grid;
use App\Attributes\Form;

class CrudAttributesService
{
    protected function __construct(
        public readonly ?string $grid,
        protected readonly array $byPath,
        protected readonly array $byNamespace,
    ) {
    }

    public static function init(array $classes, ?string $grid = ''): self
    {
        $models = Cache::get(md5(static::class), function () use ($classes) {
            $models = [];

            foreach ($classes as $class) {
                $reflectionClass = new \ReflectionClass($class);

                $paginatorAttribute = $reflectionClass->getAttributes(Grid\Paginator::class);
                $paginatorAttribute = array_shift($paginatorAttribute)?->newInstance();

                $properties = $reflectionClass->getProperties();
                $methods = $reflectionClass->getMethods();

                $columnAttributes = array_merge(
                    self::mapReflection($properties, Grid\Column::class),
                    self::mapReflection($methods, Grid\Column::class),
                );
                $fieldAttributes = array_merge(
                    self::mapReflection($properties, Form\Field::class),
                    self::mapReflection($methods, Form\Field::class),
                );

                $massActionAttributes = self::mapReflection($methods, Grid\MassAction::class);

                $info = new \stdClass();
                $info->namespace = $reflectionClass->getName();
                $info->path = $paginatorAttribute->getPath() ?? $reflectionClass->getShortName();
                $info->attributes = new class(
                    $reflectionClass,
                    $paginatorAttribute,
                    (array)$columnAttributes,
                    (array)$fieldAttributes,
                    (array)$massActionAttributes,
                ) {
                    /**
                     * @param Grid\Paginator $paginator
                     * @param Grid\Column[] $columns
                     * @param Form\Field[] $fields
                     */
                    public function __construct(
                        public readonly \ReflectionClass $reflectionClass,
                        public readonly Grid\Paginator   $paginator,
                        public readonly array            $columns,
                        public readonly array            $fields,
                        public readonly array            $massActions,
                    ) {
                    }
                };

                $models[] = $info;
            }

            return $models;
        });

        return new static(
            $grid,
            array_column($models, null, 'path'),
            array_column($models, null, 'namespace'),
        );
    }

    private static function mapReflection(array $map, string $attributes)
    {
        $mapped = array_map(function ($reflection) use ($attributes) {
            /** @var \ReflectionAttribute|array $attribute */
            $attribute = $reflection->getAttributes($attributes);
            $attribute = array_shift($attribute);
            if (empty($attribute)) {
                return null;
            }

            $arguments = [];
            $attributeArguments = $attribute->getArguments();
            $reflectionClass = new \ReflectionClass($attribute->getName());
            foreach ($reflectionClass->getConstructor()->getParameters() as $reflectionParameter) {
                $argument = @$attributeArguments[$reflectionParameter->getPosition()]
                    ?? @$attributeArguments[$reflectionParameter->getName()]
                    ?? (!$reflectionParameter->isOptional() ?: $reflectionParameter->getDefaultValue());
                if ($reflectionParameter->getName() === 'reflection') {
                    $argument = $reflection;
                }

                $arguments[$reflectionParameter->getPosition()] = $argument;
            }
            return $reflectionClass->newInstanceArgs($arguments);
        }, $map);
        return array_filter($mapped);
    }

    public function getInfoByPath(string $path)
    {
        $info = @$this->byPath[$path];
        if (!$info) {
            throw new ModelNotFoundException($path);
        }
        return $info;
    }

    public function getInfoByNamespace(string $namespace)
    {
        $info = @$this->byNamespace[$namespace];
        if (!$info) {
            throw new ModelNotFoundException($namespace);
        }
        return $info;
    }

    public function getModel(string $grid = null)
    {
        $grid = $grid ?? $this->grid;
        return $this->getInfoByPath($grid)->attributes;
    }

    public function getListProps(): array
    {
        return [
            'models' => array_keys($this->byPath),
        ];
    }

    public function getGridProps(string $grid = null): ?array
    {
        $model = $this->getModel($grid);
        /** @var Grid\Column[] $columns */
        $columns = $model->columns;
        usort($columns, fn (Grid\Column $a, Grid\Column $b) => $a->sortNumber <=> $b->sortNumber);

        return [
            'size' => $model->paginator->getSize(),
            'columns' => array_map(fn ($column) => $column->toProp($this), $columns),
            ...$this->getListProps(),
        ];
    }

    /**
     * @param string|null $grid
     * @return Grid\MassAction[]
     */
    public function getGridActions(string $grid = null): ?array
    {
        $model = $this->getModel($grid);
        $actions = array_map(fn(Grid\MassAction $massAction) => [
            (string)__($massAction->label),
            $massAction,
        ], $model->massActions);
        return array_column($actions, 1, 0);
    }

    public function getGridActionsProps(string $grid = null): ?array
    {
        $model = $this->getModel($grid);
        $actions = array_map(fn(Grid\MassAction $massAction) => (string)__($massAction->label), $model->massActions);

        return [
            'actions' => array_values($actions),
        ];
    }

    public function getFormProps(): ?array
    {
        $model = $this->getModel();
        return [
            'fields' => array_values(array_map(fn ($field) => $field->toProp($this), $model->fields)),
            ...$this->getListProps(),
        ];
    }

    /**
     * @param string $grid
     * @return array<string, Form\Field>|null
     */
    public function getFields(): ?array
    {
        $model = $this->getModel();

        /** @var Form\Field[] $fields */
        $fields = $model->fields;
        return array_column(
            array_map(fn($field) => [$field->getName(), $field], $fields),
            1,
            0
        );
    }

    public function getColumns(): ?array
    {
        $model = $this->getModel();

        /** @var Grid\Column[] $fields */
        $fields = $model->columns;
        return array_column(
            array_map(fn($field) => [$field->getName(), $field], $fields),
            1,
            0
        );
    }

    public function getClassName(): ?string
    {
        $model = $this->getModel();
        /** @var \ReflectionClass $reflectionClass */
        $reflectionClass = $model->reflectionClass;
        return $reflectionClass->getName();
    }

    public function getFormValidation(string $grid = null)
    {
        /** @var Form\Field[] $fields */
        $model = $this->getModel();
        $fields = $model->fields;
        $fields = array_map(fn ($field) => [$field->getName(), $field->validationRules], $fields);
        return array_column($fields, 1, 0);
    }
}
