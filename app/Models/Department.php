<?php

namespace App\Models;

use App\Enums\TypeDelete;
use App\Scopes\GetDataByUserIdScope;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Department extends Model
{
    use HasFactory ,SoftDeletes;
    protected $table ='departments';
    protected $guarded = [];

    /**
     * @return void
     */

    protected static function booted():void
    {
//        static::addGlobalScope(new GetDataByUserIdScope());
    }

    /**
     * @return BelongsTo
     */

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class ,'user_id' ,'id');
    }

    /**
     * @return BelongsTo
     */

    /**
     * @return BelongsToMany
     */

    public function users():BelongsToMany
    {
        return $this->belongsToMany(User::class,'user_department' ,'department_id' ,'user_id' );
    }

    /**
     * @return HasMany
     */

    public function children():HasMany
    {
        return $this->hasMany(Department::class ,'parent_id' ,'id');
    }

    /**
     * @return HasMany
     */

    public function childrenDepartment():HasMany
    {
        return $this->children()->with('childrenDepartment')->with('user');
    }

    /**
     * @return BelongsTo
     */

    public function parent():BelongsTo
    {
        return $this->belongsTo(Department::class ,'parent_id' ,'id');
    }

    /**
     * @param Builder $query
     * @param $keyword
     * @return Builder
     */

    public function scopeSearchName(Builder $query , $keyword):Builder
    {
        if (!empty($keyword))
        {
            return $query->where('name' ,'like','%'.$keyword.'%');
        }
        return $query;
    }



    public function scopeCheckTrashed($query , $type)
    {
        if ($type == TypeDelete::DELETE)
        {
            return $query->onlyTrashed();
        }
        return $query;
    }


    public function scopeGetIdsDepartment($query , $ids)
    {
        return $query->whereIn('id' , $ids);
    }

    /**
     * @param  Builder  $query
     * @return void
     */

    public function scopeQueryUserDepartment(Builder $query)
    {
        $userIds = Auth::user()->childrenUser->pluck('id')->merge(Auth::id());
        $query->selectRaw('departments.*')->leftJoin('user_department' ,'departments.id' ,'=' , 'user_department.department_id')
                ->where(function ($query) use($userIds) {
                    $query->whereIn('departments.user_id' , $userIds);
                    $query->orWhere('user_department.user_id' ,Auth::id());
                });
    }

    /**
     * @param $data
     * @param $id
     * @param $level
     * @return array
     */

    static function dataTree($data, $id, $level = 0)
    {
        $result = array();
        foreach ($data as $item) {
            if ($item->parent_id == $id) {
                $item['level'] = $level;
                $result[]      = $item;
                $child         = self::dataTree($item->childrenDepartment, $item->id, $level + 1);
                $result        = array_merge($result, $child);
            }
            unset($item);
        }

        return $result;
    }
}
