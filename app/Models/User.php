<?php

namespace App\Models;

use App\Enums\TypeDelete;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable , SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * @return Attribute
     */

    protected function password():Attribute
    {
        return Attribute::set(fn(string $value) => Hash::make($value));
    }

    /**
     * @return HasMany
     */

    public function children():HasMany
    {
        return $this->hasMany(User::class ,'parent_id' ,'id');
    }

    /**
     * @return HasMany
     */

    public function childrenUser():HasMany
    {
        return $this->children()->with('childrenUser');
    }

    /**
     * @return BelongsToMany
     */

    public function departments():BelongsToMany
    {
        return $this->belongsToMany(Department::class,'user_department' ,'user_id' ,'department_id' );
    }

    public function departmentsOrUser():HasMany
    {
        return $this->hasMany(Department::class,'user_id' ,'id');
    }

    /**
     * @return BelongsTo
     */

    public function parent()
    {
        return $this->belongsTo(User::class ,'parent_id' ,'id');
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
}
