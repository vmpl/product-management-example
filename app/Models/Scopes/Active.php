<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\App;

class Active implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        /** @var Router $router */
        $router = App::get('router');
        $middlewares = $router->current()?->computedMiddleware ?? [];
        if (in_array('api', $middlewares)) {
            $builder->where('active', '=', true);
        }
    }
}

