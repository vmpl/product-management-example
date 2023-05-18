<?php declare(strict_types=1);

namespace App\Attributes\Form;

use App\Attributes\Form\Field\Component;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class Field implements \App\Attributes\PropAttribute
{
    public function __construct(
        protected readonly string $name,
        protected readonly string $label,
        protected readonly Component $component = Component::Input,
    ) {
    }

    public function toProp(): array
    {
        return [
            'name' => $this->name,
            'label' => (string)__($this->label),
            'component' => $this->component->getConfig()->toProp(),
        ];
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
