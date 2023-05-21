<?php declare(strict_types=1);

namespace App\Attributes\Form;

use App\Attributes\Form\Field\Component;
use App\Providers\CrudAttributesService;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD)]
class Field implements \App\Attributes\PropAttribute
{
    public function __construct(
        protected readonly string $label,
        protected readonly Component $component = Component::Input,
        protected \ReflectionProperty | \ReflectionMethod | null $reflection = null,
    ) {
    }

    public function toProp(CrudAttributesService $attributesService): array
    {
        return [
            'name' => $this->getName(),
            'label' => (string)__($this->label),
            'component' => $this->component->getConfig($this)->toProp($attributesService),
        ];
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->reflection->getName();
    }

    public function setReflection(\ReflectionProperty | \ReflectionMethod $reflection): self
    {
        $this->reflection = $reflection;
        return $this;
    }

    /**
     * @return \ReflectionMethod|\ReflectionProperty|null
     */
    public function getReflection(): \ReflectionMethod|\ReflectionProperty|null
    {
        return $this->reflection;
    }
}
