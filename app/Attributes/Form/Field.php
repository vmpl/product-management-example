<?php declare(strict_types=1);

namespace App\Attributes\Form;

use App\Attributes\Form\Field\Component;
use App\Providers\CrudAttributesService;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD)]
class Field implements \App\Attributes\PropAttribute
{
    protected \App\Attributes\PropAttribute $component;

    public function __construct(
        protected readonly string $label,
        protected \ReflectionProperty | \ReflectionMethod $reflection,
        Component $component = Component::Input,
    ) {
        $this->component = $component->getConfig($this);
    }

    public function toProp(CrudAttributesService $attributesService): array
    {
        return [
            'name' => $this->getName(),
            'label' => (string)__($this->label),
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

    public function parse($input)
    {
        return $this->component->parse($input);
    }

    /**
     * @return \ReflectionMethod|\ReflectionProperty|null
     */
    public function getReflection(): \ReflectionMethod|\ReflectionProperty|null
    {
        return $this->reflection;
    }
}
