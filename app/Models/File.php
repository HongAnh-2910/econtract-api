<?php

namespace App\Models;

use App\Enums\DocumentStatus;
use App\Enums\TypeDelete;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class File extends Model
{
    use HasFactory , SoftDeletes;

    protected $table = 'files';
    protected $guarded = [];


    public function scopeByUserIdOrUserIdShare($query)
    {
        return $query->where('user_id' , Auth::id())->orWhereHas('users' , function ($query){
            $query->where('user_id', Auth::id());
        });
    }

    /**
     * @return BelongsTo
     */

    public function folder():BelongsTo
    {
        return $this->belongsTo(Folder::class ,'folder_id' ,'id');
    }

    /**
     * @return BelongsTo
     */

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class ,'user_id' ,'id');
    }

    /**
     * @return BelongsToMany
     */

    public function users():BelongsToMany
    {
        return $this->belongsToMany(User::class ,'file_share' ,'file_id' , 'user_id');
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
     * @param $query
     * @param $ids
     * @return mixed
     */

    public function scopeGetByIds($query , $ids)
    {
        return $query->whereIn('id' , $ids);
    }

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
