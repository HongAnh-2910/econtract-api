<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Application extends Model
{
    use HasFactory;

    protected $table = 'applications';

    public $primaryKey = 'id';

    protected $guarded =[];

    /**
     * @return BelongsTo
     */

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class ,'user_id' , 'id');
    }

    /**
     * @return HasMany
     */
    public function dateTimeApplications():HasMany
    {
        return $this->hasMany(ApplicationDate::class ,'application_id' ,'id');
    }

    /**
     * @return BelongsToMany
     */

    public function users():BelongsToMany
    {
        return $this->belongsToMany(User::class ,'consider_applications' ,'application_id' ,'user_id');
    }
}
