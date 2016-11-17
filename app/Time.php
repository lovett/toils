<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Traits\Search;

/**
 * Eloquent model for the times table.
 */
class Time extends Model
{
    use SoftDeletes, Search;

    /**
     * Fields that can be used for searching.
     *
     * Keys are field aliases suitable for use in UI.
     * Values are qualified field names suitable for use in SQL queries.
     *
     * @var array
     */
    public static $searchables = [
        'summary' => 'times.summary',
        'start' => 'times.start',
        'minutes' => 'times.mintues',
        'end' => 'times.end',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'start',
        'minutes',
        'summary',
        'estimatedDuration',
        'project_id',
    ];

    /**
     * Mapping between attributes and data types.
     *
     * @var array
     */
    protected $casts = [
        'start' => 'datetime',
        'minutes' => 'integer',
        'summary' => 'string',
        'estimatedDuration' => 'integer',
    ];

    /**
     * The attributes that are datetimes.
     *
     * @var array
     */
    protected $dates = [
        'deleted_at' => 'datetime',
    ];

    /**
     * Master query for getting a list of records.
     *
     * @param Builder $builder The query to start with
     *
     * @return Relation
     */
    public static function listing(Builder $builder)
    {
        $builder = $builder->with('project');
        $builder = $builder->orderBy('start', 'desc');

        return $builder;
    }

    /**
     * User associated with the time entry.
     *
     * @return HasOne
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * Project associated with the time entry.
     *
     * @return HasOne
     */
    public function project()
    {
        return $this->belongsTo('App\Project');
    }

    /**
     * Custom accessor to set default value for start date.
     *
     * @param string $value The value stored in the database
     *
     * @return Carbon;
     */
    public function getStartAttribute($value)
    {
        if (empty($value)) {
            $value = 'now';
        }

        return new Carbon($value);
    }

    /**
     * Custom accessor to convert null to zero.
     *
     * @param int|null $value The value stored in the database
     *
     * @return integer;
     */
    public function getMinutesAttribute($value)
    {
        if (is_numeric($value) === false) {
            $value = 0;
        }

        return $value;
    }

    /**
     * Custom accessor to calculate end time from start and duration.
     *
     * @return Carbon;
     */
    public function getEndAttribute()
    {
        if ($this->minutes === 0) {
            return null;
        }

        $end = clone $this->start;

        return $end->addMinutes($this->minutes);
    }

    /**
     * Custom accessor to calculate estimate accuracy as a percentage.
     *
     * @return int|null A number between 0 and 100
     */
    public function getAccuracyAttribute()
    {
        $values = [
            $this->estimatedDuration,
            $this->minutes,
        ];

        if (in_array(0, $values)) {
            return null;
        }

        $max = max($values);
        $min = min($values);

        return round(($min / $max), 2) * 100;
    }

    /**
     * Tally time by month with no gaps.
     *
     * @param Project $project    Project model instance
     * @param User    $user       User model instance
     * @param int     $monthCount How many months to go back from present
     *
     * @return array
     */
    public static function forProjectAndUserByMonth(
        Project $project,
        User $user,
        $monthCount = 6
    ) {
        if (empty($monthCount)) {
            $minStart = self::where('project_id', $project->getKey())
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

        $result = DB::select(
            $query,
            [
                'range' => (abs($monthCount) * -1).' months',
                'projectId' => $project->getKey(),
                'userId' => $user->getKey(),
            ]
        );

        $result = collect($result)->reduce(
            function ($accumulator, $item) {
                $accumulator[$item->dt] = (int) $item->minutes;

                return $accumulator;
            },
            []
        );

        return $result;
    }
}
