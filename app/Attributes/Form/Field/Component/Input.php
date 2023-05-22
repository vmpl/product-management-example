<?php

namespace App\Attributes\Form\Field\Component;

use App\Attributes\FieldComponent;

class Input extends FieldComponent
{
    protected function __construct()
    {
        $props = new \stdClass();
        $props->outlined = true;

        parent::__construct($props);
    }

    public static function getType(): string
    {
        return 'q-input';
    }

    static function init(\ReflectionMethod|\ReflectionProperty $reflection = null): static
    {
        return new static();
    }
}
