<?php

namespace App\Models;

use App\Enums\DocumentStatus;
use App\Enums\TypeDelete;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Folder extends Model
{
    use HasFactory , SoftDeletes;
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

    /**
     * @param $query
     * @param $parentIds
     * @return mixed
     */

    public function scopeGetByParents($query , $parentIds)
    {
        return $query->whereIn('parent_id' , $parentIds);
    }

    /**
     * @param $query
     * @param $parentId
     * @return mixed
     */

    public function scopeGetByParent($query , $parentId)
    {
        return $query->where('parent_id' , $parentId);
    }

    /**
     * @param $query
     * @param $ids
     * @return mixed
     */

    public function scopeGetByIds($query , $ids)
    {
        return $query->whereIn('id' , $ids);
    }

    /**
     * @param $query
     * @param $id
     * @return mixed
     */

    public function scopeGetById($query , $id)
    {
        return $query->where('id' , $id);
    }

    /**
     * @param $query
     * @param $type
     * @return mixed
     */

    public function scopeCheckTrashed($query , $type)
    {
        if ($type == TypeDelete::DELETE)
        {
            return $query->onlyTrashed();
        }
        return $query;
    }

    /**
     * @param $status
     * @param $query
     * @return mixed
     */

    public function scopeFilterStatus($query , $status)
    {
        if ($status == DocumentStatus::TRASH) {
            return $query->onlyTrashed()->ByUserIdOrUserIdShare();
        } elseif ($status == DocumentStatus::ALL_PRIVATE) {
            return $query->where('user_id', Auth::id());
        } elseif ($status == DocumentStatus::SHARE) {
            return $query->whereHas('users', function ($query) {
                $query->where('user_id', Auth::id());
            });
        }
        return $query->ByUserIdOrUserIdShare();
    }

}
