<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationDate extends Model
{
    use HasFactory;
    protected $table ='date_time_of_applications';
    public $primaryKey = 'id';

    /**
     * @return BelongsTo
     */

    public function application()
    {
        return $this->belongsTo(Application::class ,'application_id' ,'id');
    }
}
