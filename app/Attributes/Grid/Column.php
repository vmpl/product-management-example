<?php declare(strict_types=1);

namespace App\Attributes\Grid;

use App\Providers\CrudAttributesService;

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

    public function toProp(CrudAttributesService $attributesService): array
    {
        return [
            'name' => $this->name,
            'field' => $this->name,
            'label' => (string)__($this->label),
            'sortable' => $this->sortable,
            'searchable' => $this->searchable,
        ];
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isSearchable(): bool
    {
        return $this->searchable;
    }
}
