<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Project;
use App\User;

class Time extends Model
{

    use SoftDeletes;

    protected $fillable = [
        'start',
        'minutes',
        'summary',
        'estimated_duration',
    ];

    protected $casts = [
        'start' => 'datetime',
        'minutes' => 'integer',
        'summary' => 'string',
        'estimated_duration' => 'integer'
    ];

    protected $dates = [
        'deleted_at' => 'datetime'
    ];

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

    public function getEndAttribute()
    {
        if (!$this->start) {
            return null;
        }

        $end = clone $this->start;
        return $end->addMinutes($this->minutes);
    }

    public function scopeMonthlyHours($query, $months)
    {
        $query->select(
            DB::raw('strftime("%Y-%m", start) as yearmonth'),
            'minutes'
        );
        $query->where('start', '>', DB::raw("date('now', 'start of month', '-{$months} month')"));

        $query->groupBy('yearmonth');
        $query->orderBy('yearmonth', 'desc');
        return $query;
    }

    static public function forProjectAndUserByMonth(Project $project, User $user, $monthCount=null)
    {

        if (empty($monthCount)) {
            $minStart = Time::where('project_id', $project->getKey())
                      ->where('user_id', $user->getKey())
                      ->min('start');

            if (empty($minStart)) {
                return [];
            }

            $monthCount = Carbon::now()->diffInMonths(Carbon::parse($minStart));
        }

        $query = "
        WITH RECURSIVE
            tally_range(dt) AS (
                SELECT date('now', 'start of month')
                UNION ALL
                SELECT date(dt, '-1 month')
                FROM tally_range
                WHERE dt > date('now', 'start of month', :range)
            ),
            tallies(dt, minutes) AS (
                SELECT date(start, 'start of month') AS dt, SUM(minutes)
                FROM times
                WHERE project_id=:projectId
                AND user_id=:userId
                GROUP BY dt
            )
            SELECT * FROM tally_range NATURAL LEFT OUTER JOIN tallies
        ";

        $result = DB::select($query, [
            'range' => abs($monthCount) * -1  . ' months',
            'projectId' => $project->getKey(),
            'userId' => $user->getKey(),
        ]);

        $result = collect($result)->reduce(function ($accumulator, $item) {
            $accumulator[$item->dt] = (int)$item->minutes;
            return $accumulator;
        }, []);

        return $result;
    }
}
