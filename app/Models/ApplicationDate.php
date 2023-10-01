<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationDate extends Model
{
    use HasFactory;
    protected $table ='date_time_of_applications';
    public $primaryKey = 'id';

    protected $guarded = [];

    /**
     * @return BelongsTo
     */

    public function application():BelongsTo
    {
        return $this->belongsTo(Application::class ,'application_id' ,'id');
    }

    /**
     * @param $query
     * @param string $month
     * @return mixed
     */
    public function scopeByMonth($query , string $month)
    {
        return $query->whereMonth('created_at' ,$month);
    }

    /**
     * @param $query
     * @param string $year
     * @return mixed
     */

    public function scopeByYear($query , string $year)
    {
        return $query->whereYear('created_at' , $year);
    }

    /**
     * @param $query
     * @param string $month
     * @param string $year
     * @return mixed
     */

    public function scopeByMonthAndYear($query ,string $month , string $year)
    {
        return $query->ByMonth($month)->ByYear($year);
    }
}
