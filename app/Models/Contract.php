<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
}
