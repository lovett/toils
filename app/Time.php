<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Time extends Model
{

    use SoftDeletes;

    protected $fillable = [
        'start',
        'end',
        'summary',
        'estimated_duration',
    ];

    protected $casts = [
        'start' => 'datetime',
        'end' => 'datetime',
        'summary' => 'string',
        'estimated_duration' => 'integer'
    ];

    protected $dates = [
        'deleted_at' => 'datetime'
    ];

    public function __construct()
    {
        $this->durationHours = DB::raw('(strftime("%s", end) - strftime("%s", start) * 1.0)/3600 as hours');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function project()
    {
        return $this->belongsTo('App\Project');
    }

    /* Disabled until invoice model has been created
    public function invoice()
    {
        return $this->belongsTo('App\Invoice');
    }*/

    public function scopeByUser($query, User $user)
    {
        return $query->where('user_id', $user->id);
    }

    public function scopeMonthlyHours($query, $order=null)
    {
        $yearMonth = DB::raw('strftime("%Y-%m", start) as yearmonth');

        $query->select($yearMonth, $this->durationHours);
        $query->groupBy('yearmonth');

        if ($order) {
            $query->orderBy('yearmonth', $order);
        }
        return $query;
    }
}
