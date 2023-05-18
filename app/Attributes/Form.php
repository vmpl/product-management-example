<?php declare(strict_types=1);

namespace App\Attributes;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Form
{
    public function __construct(
        protected readonly ?string $path = null,
    ) {
    }
}
