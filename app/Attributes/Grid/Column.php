<?php declare(strict_types=1);

namespace App\Attributes\Grid;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Column
{
    public function __construct(
        protected readonly string $name,
        protected readonly string $label,
        protected readonly bool $searchable = true,
        protected readonly bool $sortable = true,
    ) {
    }

    public function toComponent(): array
    {
        return [
            'name' => $this->name,
            'label' => (string)__($this->label),
            'searchable' => $this->searchable,
            'sortable' => $this->sortable,
        ];
    }
}
