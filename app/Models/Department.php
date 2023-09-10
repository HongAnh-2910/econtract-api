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
        static::addGlobalScope(new GetDataByUserIdScope());
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
        return $this->children()->with('childrenDepartment');
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
}
