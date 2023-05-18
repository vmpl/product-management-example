<?php declare(strict_types=1);

namespace App\Attributes\Grid;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class Column implements \App\Attributes\PropAttribute
{
    public function __construct(
        protected readonly string $name,
        protected readonly string $label,
        protected readonly bool $searchable = true,
        protected readonly bool $sortable = true,
    ) {
    }

    public function toProp(): array
    {
        return [
            'name' => $this->name,
            'label' => (string)__($this->label),
            'searchable' => $this->searchable,
            'sortable' => $this->sortable,
        ];
    }
}
