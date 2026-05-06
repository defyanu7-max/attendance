<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class UnitScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (app()->runningInConsole()) return;
        if (! auth()->hasUser()) return;

        $user = auth()->user();

        if ($user->role === 'superadmin') return;
        if (! $user->unit_id) return;

        $builder->where($model->getTable() . '.unit_id', $user->unit_id);
    }
}
