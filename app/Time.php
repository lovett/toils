<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Traits\Search;
use App\Invoice;
use stdClass;
use App\Helpers\TimeHelper;
use App\Tag;

/**
 * Eloquent model for the times table.
 */
class Time extends Model
{
    use SoftDeletes, Search;

    /**
     * Default values for newly-created instances.
     *
     * @var array
     */
    protected $attributes = [
        'minutes' => 0,
    ];

    /**
     * When a time entry is updated, mark its project as updated as well.
     *
     * Although this only applies to update and not create, that
     * should be fine since a time entry is usually started and
     * finished at separate times. A create-only time entry would be
     * less common.
     *
     * @var array
     */
    protected $touches = [
        'project',
    ];


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
        'invoice' => 'times.invoice_id',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'start',
        'end',
        'minutes',
        'summary',
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
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'deleted_at',
        'start',
        'updated_at',
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
     * Query scope to restrict time entries to the current week
     *
     * @param Builder $query An existing query.
     * @param int $limit If greater than zero, the max number of records to return.
     *
     * @return Builder
     */
    public function scopeThisWeek($query)
    {
        $now = new Carbon();
        $query->where('start', '>=', $now->startOfWeek());
        return $query;
    }

    /**
     * Query scope to restrict by recentness
     *
     * @param Builder $query An existing query.
     * @param int $limit If greater than zero, the max number of records to return.
     *
     * @return Builder
     */
    public function scopeNewest($query, $limit=0)
    {
        $query->orderBy('start', 'desc');

        if ($limit > 0) {
            $query->limit($limit);
        }
        return $query;
    }

    /**
     * Query scope to filter by invoice
     *
     * @param Builder $query An existing query
     * @param Invoice $invoice An invoice
     *
     * @return Builder
     */
    public function scopeForInvoice($query, Invoice $invoice)
    {
        $query->where('invoice_id', $invoice->id);
        return $query;
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
     * Invoice associated with the time entry.
     *
     * @return HasOne
     */
    public function invoice()
    {
        return $this->belongsTo('App\Invoice');
    }

    /**
     * Tags associated with the time entry
     */
    public function tags()
    {
        return $this->morphToMany('App\Tag', 'taggable');
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
     * @return Carbon|null;
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
     * Custom mutator to calculate minutes from end and start times.
     *
     * @return int|null;
     */
    public function setEndAttribute(Carbon $value = null)
    {
        if (empty($this->start)) {
            return nulll;
        }

        $this->attributes['minutes'] = $value->diffInMinutes($this->start);
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
     * Tally time by time interval with no gaps.
     *
     * @param Project $project Project model instance
     * @param User $user User model instance
     * @param string $timeInterval Time scale for the tallies: week, month, or year
     * @param int $intervalCount How many intervals to go back from present. If null, take everything.
     *
     * @return array
     */
    public static function forProjectAndUserByInterval(
        Project $project,
        User $user,
        $timeInterval = 'month',
        $intervalCount = null
    ) {
        switch($timeInterval) {
            case 'week':
                $diffMethod = 'diffInWeeks';
                $sqlDateModifier = 'weekday 6';
                $sqlInterval = 'day';
                $sqlDateSelector = '-7 day';
                $intervalMultiplier = 7;
                break;
            case 'month':
                $diffMethod = 'diffInMonths';
                $sqlDateModifier = 'start of month';
                $sqlInterval = 'month';
                $sqlDateSelector = '-1 month';
                $intervalMultiplier = 1;
                break;
            case 'year':
                $diffMethod = 'diffInYears';
                $sqlDateModifier = 'start of year';
                $sqlInterval = 'year';
                $sqlDateSelector = '-1 year';
                $intervalMultiplier = 1;
                break;
            default:
                throw new InvalidArgumentException('invalid timeInterval');
        }

        if (is_null($intervalCount)) {
            $minStart = self::where('project_id', $project->getKey())
                      ->where('user_id', $user->getKey())
                      ->min('start');

            if (empty($minStart)) {
                return [];
            }

            $intervalCount = Carbon::now()->$diffMethod(Carbon::parse($minStart));
        }

        $query = "
        WITH RECURSIVE
            tally_range(dt) AS (
                SELECT date('now', '{$sqlDateModifier}')
                UNION ALL
                SELECT date(dt, '{$sqlDateSelector}')
                FROM tally_range
                WHERE dt > date('now', '{$sqlDateModifier}', :range)
            ),
            tallies(dt, minutes) AS (
                SELECT date(start, '{$sqlDateModifier}') AS dt, SUM(minutes)
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
                'range' => (abs($intervalCount) * $intervalMultiplier * -1) . ' ' . str_plural($sqlInterval),
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

    public function attachInvoice()
    {
        DB::beginTransaction();

        $this->invoice()->dissociate();

        $invoice = Invoice::where('start', '>=', $this->start);
        $invoice->where('end', '<=', $this->start);
        $invoice->where('project_id', $this->project_id);

        $this->invoice()->associate($invoice->first());

        DB::commit();
    }

    /**
     * Hints for autocomplete
     *
     * Returns an object whose values can be used to auto-populate
     * form fields during create or update.
     *
     * @return object An object with suggested and previously-used values.
     */
    public function getSuggestionAttribute()
    {
        $now = Carbon::now();

        $suggestion = new stdClass();

        $suggestion->previous = [
            'estimatedDuration' => $this->estimatedDuration,
            'start' => TimeHelper::date($this->start),
            'summary' => $this->summary,
        ];

        $suggestion->suggested = [
            'estimatedDuration' => $this->estimatedDuration,
            'start' => TimeHelper::date($now),
            'summary' => $this->summary,
        ];

        return $suggestion;
    }

    /**
     * Round start time to nearest 5-minute interval
     *
     */
    public function setStartAttribute($value=null)
    {
        if (empty($value)) {
            return null;
        }

        $carbonInstance = new Carbon($value);

        $this->attributes['start'] = TimeHelper::roundToNearestMinuteMultiple($carbonInstance, 5);
    }

    /**
     * Default start time to nearest 5-minute interval
     *
     */
    public function getStartAttribute(string $value=null)
    {
        if (empty($value)) {
            return null;
        }

        $carbonInstance = new Carbon($value);
        return TimeHelper::roundToNearestMinuteMultiple($carbonInstance, 5);
    }

    /**
     * Round duration minutes to nearest 5-minute interval
     */
    public function setMinutesAttribute(int $value)
    {
        $this->attributes['minutes'] = TimeHelper::roundToNearestMultiple($value, 5);
    }

    /**
     * Computed accessor for converting a collection of tags to a
     * comma-delimited list.
     */
    public function getTagListAttribute()
    {
       return $this->tags->implode('name', ', ');
    }


    public function syncTagsFromList($tagList)
    {
        $tags = Tag::createFromList($tagList);
        $this->tags()->sync($tags);
    }

}
