<?php

namespace App\Attributes\Grid\Column\Component;

use App\Attributes\GridComponent;

class Image extends GridComponent
{
    public static function getType(): string
    {
        return 'q-img';
    }

    public static function init(\App\Attributes\Grid\Column $column): static
    {
        $props = new \stdClass();
        $props->width = '200px';
        $props->height = '200px';
        return new static($props);
    }
}
