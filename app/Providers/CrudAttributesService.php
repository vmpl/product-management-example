<?php declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\Facades\Cache;
use App\Attributes\Grid;
use App\Attributes\Form;

class CrudAttributesService
{
    protected function __construct(
        protected readonly \stdClass $models,
    ) {
    }

    public static function init(array $classes): self
    {
        $models = Cache::get(md5(static::class), function () use ($classes) {
            $models = new \stdClass();

            foreach ($classes as $class) {
                $reflectionClass = new \ReflectionClass($class);

                $paginatorAttribute = $reflectionClass->getAttributes(Grid\Paginator::class);
                $paginatorAttribute = array_shift($paginatorAttribute)?->newInstance();

                $columnAttributes = $reflectionClass->getAttributes(Grid\Column::class);
                $columnAttributes = array_map(fn ($attribute) => $attribute->newInstance(), $columnAttributes);

                $fieldAttributes = $reflectionClass->getAttributes(Form\Field::class);
                $fieldAttributes = array_map(fn ($attribute) => $attribute->newInstance(), $fieldAttributes);

                $modelName = $paginatorAttribute->getPath() ?? $reflectionClass->getShortName();
                $models->$modelName = new class($class, $paginatorAttribute, $columnAttributes, $fieldAttributes) {
                    /**
                     * @param Grid\Paginator $paginator
                     * @param Form\Column[] $columns
                     * @param Form\Field[] $fields
                     */
                    public function __construct(
                        public readonly string         $className,
                        public readonly Grid\Paginator $paginator,
                        public readonly array          $columns,
                        public readonly array          $fields,
                    ) {
                    }
                };
            }

            return $models;
        });

        return new static($models);
    }

    public function getListProps(): array
    {
        $models = (array)$this->models;
        return [
            'models' => array_keys($models),
        ];
    }

    public function getGridProps(string $grid): ?array
    {
        $model = $this->models->$grid;
        if (!$model) {
            return null;
        }

        return [
            'size' => $model->paginator->getSize(),
            'columns' => array_map(fn ($column) => $column->toProp(), $model->columns),
            ...$this->getListProps(),
        ];
    }

    public function getFormProps(string $grid): ?array
    {
        $model = $this->models->$grid;
        if (!$model) {
            return null;
        }

        return [
            'fields' => array_map(fn ($field) => $field->toProp(), $model->fields),
            ...$this->getListProps(),
        ];
    }

    public function getFieldNames(string $grid): ?array
    {
        $model = $this->models->$grid;
        if (!$model) {
            return null;
        }

        return array_map(fn ($field) => $field->getName(), $model->fields);
    }

    public function getClassName(string $grid): ?string
    {
        $model = $this->models->$grid;
        if (!$model) {
            return null;
        }

        return $model->className;
    }
}
