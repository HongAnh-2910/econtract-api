<?php

namespace App\Scopes;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class GetDataByUserIdScope implements Scope
{
    /**
     * @param Builder $builder
     * @param Model $model
     * @return void
     */
    public function apply(Builder $builder, Model $model):void
    {
        $userIds = Auth::user()->childrenUser->pluck('id')->merge(Auth::id());
        $builder->selectRaw('departments.*')->leftJoin('user_department' ,'departments.id' ,'=' , 'user_department.department_id')
            ->where(function ($query) use($userIds) {
                $query->whereIn('departments.user_id' , $userIds);
                $query->orWhere('user_department.user_id' ,Auth::id());
            });
    }
}
