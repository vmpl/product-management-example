<?php

namespace App\Attributes\Grid\Column\Component;

use App\Attributes\GridComponent;

class DefaultComponent extends GridComponent
{
    public static function getType(): string
    {
        return 'inline';
    }
}
