<?php

namespace App\Attributes\Grid\Column;

use App\Attributes\Grid\Column\Component\DefaultComponent;
use App\Attributes\Grid\Column\Component\Image;

enum Component
{
    case Default;
    case Image;

    public function getConfig(\App\Attributes\Grid\Column $column): \App\Attributes\GridComponent
    {
        return match ($this) {
            self::Image => Image::init($column),
            self::Default => DefaultComponent::init($column),
        };
    }
}
