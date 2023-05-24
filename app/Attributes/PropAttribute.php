<?php

namespace App\Attributes;

use App\Providers\CrudAttributesService;

interface PropAttribute
{
    public function toProp(CrudAttributesService $attributesService): array;
}
