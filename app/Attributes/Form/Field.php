<?php declare(strict_types=1);

namespace App\Attributes\Form;

use App\Attributes\Form\Field\Type;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Field
{
    public function __construct(
        protected readonly string $name,
        protected readonly Type $type = Type::Text,
    ) {
    }
}
