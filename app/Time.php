<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Eloquent model for the times table
 */
class Time extends Model
{

    use SoftDeletes;

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
    ];

    /**
     * Mapping between attributes and data types
     *
     * @var array
     */
    protected $casts = [
        'start' => 'datetime',
        'minutes' => 'integer',
        'summary' => 'string',
        'estimatedDuration' => 'integer'
    ];

    /**
     * The attributes that are datetimes
     *
     * @var array
     */
    protected $dates = [
        'deleted_at' => 'datetime'
    ];


    /**
     * Master query for getting a list of records
     *
     * @param HasMany $relation The relation to start with.
     *
     * @return HasMany
     */
    public static function listing(HasMany $relation)
    {
        $relation = $relation->with('project');
        $relation = $relation->orderBy('start', 'desc');
        return $relation;
    }

    /**
     * User associated with the time entry
     *
     * @return HasOne
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * Project associated with the time entry
     *
     * @return HasOne
     */
    public function project()
    {
        return $this->belongsTo('App\Project');
    }

    /**
     * Custom accessor to set default value for start date
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
     * Custom accessor to calculate end time from start and duration
     *
     * @return Carbon;
     */
    public function getEndAttribute()
    {
        if (empty($this->start)) {
            return null;
        }

        if (empty($this->minutes)) {
            return null;
        }

        $end = clone $this->start;
        return $end->addMinutes($this->minutes);
    }

    /**
     * Tally time by month with no gaps
     *
     * @param Project $project    Project model instance.
     * @param User    $user       User model instance.
     * @param integer $monthCount How many months to go back from present.
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
                'range'     => (abs($monthCount) * -1)  . ' months',
                'projectId' => $project->getKey(),
                'userId'    => $user->getKey(),
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
