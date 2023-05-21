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

                $columnAttributes = array_map(function ($property) {
                    $attributes = $property->getAttributes(Grid\Column::class);
                    /** @var Grid\Column $attribute */
                    $attribute = array_shift($attributes)?->newInstance();
                    $attribute?->setReflection($property);
                    return $attribute;
                }, $properties);
                $columnAttributes = array_filter($columnAttributes);

                $fieldAttributes = array_map(function ($property) {
                    $attributes = $property->getAttributes(Form\Field::class);
                    /** @var Form\Field $attribute */
                    $attribute = array_shift($attributes)?->newInstance();
                    $attribute?->setReflection($property);
                    return $attribute;
                }, $properties);

                $fieldAttributes = array_merge(array_map(function ($method) {
                    $attributes = $method->getAttributes(Form\Field::class);
                    /** @var Form\Field $attribute */
                    $attribute = array_shift($attributes)?->newInstance();
                    $attribute?->setReflection($method);
                    return $attribute;
                }, $methods), $fieldAttributes);
                $fieldAttributes = array_filter($fieldAttributes);

                $info = new \stdClass();
                $info->namespace = $reflectionClass->getName();
                $info->path = $paginatorAttribute->getPath() ?? $reflectionClass->getShortName();
                $info->attributes = new class(
                    $reflectionClass,
                    $paginatorAttribute,
                    (array)$columnAttributes,
                    (array)$fieldAttributes,
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

    public function getGridProps(string $grid = ''): ?array
    {
        $model = $this->getModel($grid);
        return [
            'size' => $model->paginator->getSize(),
            'columns' => array_map(fn ($column) => $column->toProp($this), $model->columns),
            ...$this->getListProps(),
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
}
