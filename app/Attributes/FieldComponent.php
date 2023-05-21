<?php

namespace App\Attributes;

use App\Providers\CrudAttributesService;

abstract class FieldComponent implements PropAttribute
{
    protected function __construct(
        protected readonly \stdClass $props = new \stdClass(),
    ) {
    }

    abstract static function init(\ReflectionMethod | \ReflectionProperty $reflection = null): static;

    abstract public static function getType(): string;


    public function toProp(CrudAttributesService $attributesService): array
    {
        return [
            'type' => static::getType(),
            'props' => $this->props,
        ];
    }
}
