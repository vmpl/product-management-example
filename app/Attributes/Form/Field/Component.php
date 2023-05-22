<?php declare(strict_types=1);

namespace App\Attributes\Form\Field;

use App\Attributes\Form\Field\Component\Input;
use App\Attributes\Form\Field\Component\SelectChildren;

enum Component
{
    case Input;
    case Children;

    public function getConfig(\App\Attributes\Form\Field $field): \App\Attributes\FieldComponent
    {
        return match ($this) {
            self::Input => Input::init($field->getReflection()),
            self::Children => SelectChildren::init($field->getReflection()),
        };
    }
}
