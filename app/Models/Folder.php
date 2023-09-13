<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class Folder extends Model
{
    use HasFactory;
    protected $table ='folders';

    protected $guarded = [];

    /**
     * @return void
     */

    protected static function booted():void
    {
        static::addGlobalScope('user',function (Builder $builder){
            $builder->where('user_id' ,Auth::id());
        });

    }

    /**
     * @return BelongsTo
     */

    public function user()
    {
        return $this->belongsTo(User::class ,'user_id' ,'id');
    }

    /**
     * @return BelongsTo
     */

    public function parent()
    {
        return $this->belongsTo(Folder::class ,'parent_id' ,'id');
    }

    /**
     * @param $query
     * @param $id
     * @return mixed
     */

    public function scopeById($query , $id)
    {
        return $query->where('id', $id);
    }
}
