<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;

class File extends Model
{
    use HasFactory;

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
}
