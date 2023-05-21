<?php

namespace App\Attributes\Form\Field\Component;

use App\Attributes\FieldComponent;
use App\Providers\CrudAttributesService;

class SelectChildren extends FieldComponent
{
    protected function __construct(
        protected readonly \Illuminate\Database\Eloquent\Relations\Relation $relation,
        \stdClass $props = new \stdClass()
    ) {
        parent::__construct($props);
    }

    public static function getType(): string
    {
        return 'select-children';
    }

    static function init(\ReflectionMethod|\ReflectionProperty $reflection = null): static
    {
        /** @var \Illuminate\Database\Eloquent\Relations\Relation $invoke */
        $relation = $reflection->invoke(!$reflection->isStatic()
            ? $reflection->getDeclaringClass()->newInstanceWithoutConstructor()
            : null);

        return new static($relation);
    }

    public function toProp(CrudAttributesService $attributesService): array
    {
        $component = parent::toProp($attributesService);

        $modelPath = $attributesService->getInfoByNamespace($this->relation->getModel()::class)->path;
        $gridProps = $attributesService->getGridProps($modelPath);

        $component['props']->size = $gridProps['size'];
        $component['props']->columns = $gridProps['columns'];
        $component['props']->urlFetch = route('crud.grid.fetch', ['grid' => $modelPath]);

        return $component;
    }
}
