<?php

namespace App\Models;

use App\Enums\ApplicationStatus;
use App\Http\States\Application\ApplicationState;
use App\Http\States\Application\Cancel;
use App\Http\States\Application\Pending;
use App\Http\States\Application\Success;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\ModelStates\HasStates;

class Application extends Model
{
    use HasFactory , HasStates;

    protected $table = 'applications';

    public $primaryKey = 'id';

    protected $guarded =[];

    protected $casts = [
        'status' => ApplicationState::class,
    ];

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

    /**
     * @return BelongsToMany
     */

    public function applicationFiles()
    {
        return $this->belongsToMany(File::class ,'file_application' ,'application_id' ,'file_id');
    }

    /**
     * @return BelongsTo
     */

    public function userCreateApplication():BelongsTo
    {
        return $this->belongsTo(User::class ,'user_application' ,'id' ,'id');
    }

    /**
     * @param $query
     * @param string $status
     * @return mixed
     */

    public function scopeByStatus($query , string $status)
    {
        return $query->where('status',  $status);
    }
}
