<?php declare(strict_types=1);

namespace App\Attributes\Grid;

use App\Providers\CrudAttributesService;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Column implements \App\Attributes\PropAttribute
{
    public function __construct(
        protected readonly string $label,
        protected readonly bool $searchable = true,
        protected readonly bool $sortable = true,
        protected ?\ReflectionProperty $reflection = null,
    ) {
    }

    public function toProp(CrudAttributesService $attributesService): array
    {
        return [
            'name' => $this->getName(),
            'field' => $this->getName(),
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
        return $this->reflection->getName();
    }

    /**
     * @return bool
     */
    public function isSearchable(): bool
    {
        return $this->searchable;
    }

    public function setReflection(\ReflectionProperty $reflection): self
    {
        $this->reflection = $reflection;
        return $this;
    }

    /**
     * @return \ReflectionProperty|null
     */
    public function getReflection(): ?\ReflectionProperty
    {
        return $this->reflection;
    }
}
