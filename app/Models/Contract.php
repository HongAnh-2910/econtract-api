<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contract extends Model
{
    use HasFactory;

    protected $table = 'contracts';

    protected $guarded = [];

    /**
     * @return HasMany
     */

    public function signatures():HasMany
    {
        return $this->hasMany(Signature::class ,'contract_id' ,'id');
    }

    /**
     * @return BelongsToMany
     */

    public function files():BelongsToMany
    {
        return $this->belongsToMany(File::class ,'file_contract' ,'contract_id' ,'file_id')
                    ->withPivot('base64');
    }

    /**
     * @return HasMany
     */

    public function follows():HasMany
    {
        return $this->hasMany(FollowSignature::class ,'contract_id' ,'id');
    }

    /**
     * @return BelongsTo
     */
    public function banking():BelongsTo
    {
        return $this->belongsTo(Banking::class ,'banking_id' , 'id');
    }

    /**
     * @return BelongsTo
     */

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class ,'user_id' , 'id');
    }
}
