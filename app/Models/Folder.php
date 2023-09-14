<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Folder extends Model
{
    use HasFactory;
    protected $table ='folders';

    protected $guarded = [];


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

    /**
     * @return BelongsToMany
     */

    public function users():BelongsToMany
    {
        return $this->belongsToMany(User::class ,'folder_share' ,'folder_id' , 'user_id');
    }

    /**
     * @return HasMany
     */

    public function children():HasMany
    {
        return $this->hasMany(Folder::class ,'parent_id' , 'id');
    }

    /**
     * @return HasMany
     */

    public function treeChildren():HasMany
    {
        return $this->children()->with('treeChildren');
    }

    /**
     * @return HasMany
     */

    public function files():HasMany
    {
        return $this->hasMany(File::class ,'folder_id' ,'id');
    }

    /**
     * @param $query
     * @return mixed
     */

    public function scopeByUserIdOrUserIdShare($query)
    {
        return $query->where('user_id' , Auth::id())->orWhereHas('users' , function ($query){
            $query->where('user_id', Auth::id());
        });
    }
}
