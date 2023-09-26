<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Application extends Model
{
    use HasFactory;

    protected $table = 'applications';

    public $primaryKey = 'id';

    /**
     * @return HasMany
     */
    public function dateTimeApplications()
    {
        return $this->hasMany(ApplicationDate::class ,'application_id' ,'id');
    }
}
