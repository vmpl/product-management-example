<?php

namespace App\Attributes;

use App\Providers\CrudAttributesService;

abstract class GridComponent implements PropAttribute
{
    protected function __construct(
        protected readonly \stdClass $props = new \stdClass(),
    ) {
    }

    static function init(\App\Attributes\Grid\Column $column): static
    {
        $fieldComponent = new static();
        return $fieldComponent;
    }

    abstract public static function getType(): string;


    public function toProp(CrudAttributesService $attributesService): array
    {
        return [
            'type' => static::getType(),
            'props' => $this->props,
        ];
    }
}
