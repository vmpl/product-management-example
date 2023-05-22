<?php

namespace App\Attributes\Grid;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

#[\Attribute(\Attribute::TARGET_METHOD)]
class MassAction
{
    public function __construct(
        public readonly string $label,
        protected readonly \ReflectionMethod|null $reflection = null,
    ) {
        if (!$this->reflection->isStatic()) {
            throw new \Exception('Mass method have to be static');
        }
    }

    public function invoke(Request $request)
    {
        return $this->reflection->invoke(null, [$request->input('ids')]);
    }
}
