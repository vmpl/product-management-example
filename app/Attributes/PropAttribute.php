<?php

namespace App\Attributes;

interface PropAttribute
{
    public function toProp(): array;
}
