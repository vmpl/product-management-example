<?php declare(strict_types=1);

namespace App\Attributes\Grid;

use App\Attributes\Grid\Column\Component;
use App\Providers\CrudAttributesService;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD)]
class Column implements \App\Attributes\PropAttribute
{
    public readonly \App\Attributes\GridComponent $component;

    public function __construct(
        protected readonly string $label,
        protected readonly bool $searchable = true,
        protected readonly bool $sortable = true,
        public readonly int $sortNumber = PHP_INT_MAX,
        Component $component = Component::Default,
        protected \ReflectionProperty|\ReflectionMethod|null $reflection = null,
    ) {
        $this->component = $component->getConfig($this);
    }

    public function toProp(CrudAttributesService $attributesService): array
    {
        return [
            'name' => $this->getName(),
            'field' => $this->getName(),
            'label' => (string)__($this->label),
            'sortable' => $this->sortable,
            'searchable' => $this->searchable,
            'component' => $this->component->toProp($attributesService),
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
