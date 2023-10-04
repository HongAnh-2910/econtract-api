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
use Illuminate\Support\Facades\Auth;
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

    public function scopeByType($query , float $type)
    {
        return $query->where('type' , $type);
    }

    /**
     * @param $query
     * @return mixed
     */

    public function scopeByUserLogin($query)
    {
        return $query->where(function ($query) {
            $query->where('user_application', Auth::id());
            $query->orWhere('user_id', Auth::id());
            $query->orWhereHas('users', function ($query) {
                $query->where('user_id', Auth::id());
            });
        });
    }

    /**
     * @param $query
     * @param $status
     * @return mixed
     */

    public function scopeFilterStatus($query , $status)
    {
        switch ($status) {
            case ApplicationStatus::PENDING_STR:
               return $query->ByStatus(ApplicationStatus::PENDING);
            case ApplicationStatus::SUCCESS_STR:
                return $query->ByStatus(ApplicationStatus::SUCCESS);
            case ApplicationStatus::CANCEL_STR:
                return $query->ByStatus(ApplicationStatus::CANCEL);
            case ApplicationStatus::DELETE_STR:
                break;
            case ApplicationStatus::APPLICATION_STR:
                return $query->ByType(ApplicationStatus::CREATE_APPLICATION);
            case ApplicationStatus::PROPOSAL_STR:
                return $query->ByType(ApplicationStatus::CREATE_SUGGESTION);
        }
        return $query;
    }

    /**
     * @param $query
     * @param $search
     * @return mixed
     */

    public function scopeSearchName($query , $search)
    {
        if (!empty($search))
        {
           return $query->where('code' , 'like' ,'%'.$search.'%');
        }
        return $query;
    }
}
