<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
use App\Traits\Search;
use App\Models\Invoice;
use stdClass;
use App\Helpers\TimeHelper;
use App\Models\Tag;
use InvalidArgumentException;

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
    protected $attributes = [];

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
    protected $touches = ['project'];


    /**
     * Fields that can be used for searching.
     *
     * Keys are field aliases suitable for use in UI.
     * Values are qualified field names suitable for use in SQL queries.
     *
     * @var array
     */
    public static $searchables = [
        'client' => 'clients.name',
        'project' => 'projects.name',
        'summary' => 'times.summary',
        'date' => 'times.start',
        'tag' => 'tags.name',
        'invoice' => 'invoices.number',
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
        'billable',
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
     * @return Builder
     */
    public static function listing(Builder $builder)
    {
        $joins = ($builder->getQuery()->joins) ?: [];
        $joinedTables = array_map(function ($join) {
            return $join->table;
        }, $joins);

        $builder = $builder->select('times.*');
        $builder = $builder->addSelect('clients.id as clientId');
        $builder = $builder->addSelect('clients.name as clientName');
        $builder = $builder->addSelect('projects.name as projectName');
        $builder = $builder->addSelect('invoices.number as invoiceNumber');
        $builder = $builder->addSelect('projects.id as projectId');
        $builder = $builder->addSelect('tags.name as tagName');

        if (in_array('projects', $joinedTables) === false) {
            $builder = $builder->join('projects', 'times.project_id', '=', 'projects.id');
        }

        if (in_array('invoices', $joinedTables) === false) {
            $builder = $builder->leftJoin('invoices', 'times.invoice_id', '=', 'invoices.id');
        }

        $builder = $builder->join('clients', 'projects.client_id', '=', 'clients.id');
        $builder = $builder->leftJoin('taggables', function ($join) {
            $join = $join->on('times.id', '=', 'taggables.taggable_id');
            $join = $join->where('taggables.taggable_type', '=', 'App\Models\Time');
            return $join;
        });

        $builder = $builder->leftJoin('tags', 'taggables.tag_id', '=', 'tags.id');
        $builder = $builder->with('project')->with('tags');
        $builder = $builder->orderBy('start', 'desc');
        $builder = $builder->groupBy('times.id');

        return $builder;
    }

    /**
     * Set instance defaults.
     *
     * @param array $attributes The key-value array to populate.
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $now = new Carbon();

        $this->attributes = array_merge(
            $this->attributes,
            [
                'start' => $now,
                'minutes' => 0,
                'billable' => true,
            ]
        );
    }

    /**
     * Query scope to restrict to in-progress, unfinished records.
     *
     * @param Builder  $query An existing query.
     * @param int|null $limit Maximum number of records to return.
     */
    public function scopeUnfinished(Builder $query, int $limit = null)
    {
        $query->where('minutes', 0);
        $query->orderBy('start', 'desc');

        if ($limit !== null) {
            $query->limit($limit);
        }

        return $query;
    }

    /**
     * Query scope to restrict to billable entries.
     *
     * @param Builder $query An existing query.
     */
    public function scopeBillable(Builder $query)
    {
        $query->where('billable', true);
        return $query;
    }

    /**
     * Query scope to restrict to unbillable entries.
     *
     * @param Builder $query An existing query.
     */
    public function scopeUnbillable(Builder $query)
    {
        $query->where('billable', false);
        return $query;
    }

    /**
     * Query scope to restrict time entries to the current week
     *
     * @param Builder $query An existing query.
     *
     * @return Builder
     */
    public function scopeThisWeek(Builder $query)
    {
        $now = new Carbon();
        $query->where('start', '>=', $now->startOfWeek());
        return $query;
    }

    /**
     * Query scope to restrict by recentness
     *
     * @param Builder $query An existing query.
     * @param int     $limit If greater than zero, the max number of records to return.
     *
     * @return Builder
     */
    public function scopeNewest(Builder $query, int $limit = 0)
    {
        $query->orderBy('start', 'desc');

        if ($limit > 0) {
            $query->limit($limit);
        }
        return $query;
    }

    /**
     * User associated with the time entry.
     *
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    /**
     * Project associated with the time entry.
     *
     * @return BelongsTo
     */
    public function project()
    {
        return $this->belongsTo('App\Models\Project');
    }

    /**
     * Invoice associated with the time entry.
     *
     * @return BelongsTo
     */
    public function invoice()
    {
        return $this->belongsTo('App\Models\Invoice');
    }

    /**
     * Tags associated with the time entry
     */
    public function tags()
    {
        return $this->morphToMany('App\Models\Tag', 'taggable');
    }

    /**
     * Custom accessor to convert null to zero.
     *
     * @param int|null $value The value stored in the database
     *
     * @return integer;
     */
    public function getMinutesAttribute(int $value = null)
    {
        if (is_numeric($value) === false) {
            $value = 0;
        }

        return $value;
    }

    /**
     * Custom access to determine whether an entry has an end date
     *
     * @return boolean
     */
    public function getFinishedAttribute()
    {
        return ($this->minutes > 0);
    }

    /**
     * Custom accessor to calculate end time from start and minutes.
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
     * @param Carbon $value An end date.
     *
     * @return int|null;
     */
    public function setEndAttribute(Carbon $value = null)
    {
        if (empty($this->start)) {
            return null;
        }

        if ($value === null) {
            $this->attributes['minutes'] = 0;
            return null;
        }

        $this->attributes['minutes'] = $value->diffInMinutes($this->start);
    }

    /**
     * Custom accessor to calculate estimate accuracy as a percentage.
     *
     * @return float|null A number between 0 and 100
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
     * @param Model  $model         Project or client instance
     * @param User   $user          User model instance
     * @param string $timeInterval  Time scale for the tallies: week, month, or year
     * @param int    $intervalCount How many intervals to go back from present.
     *
     * @throws InvalidArgumentException Unrecognized values for $timeInterval are rejected.
     *
     * @return array
     */
    public static function byInterval(
        Model $model,
        User $user,
        string $timeInterval = 'month',
        int $intervalCount = 6
    ) {
        $sqlDateModifier = 'weekday 6';
        $sqlInterval = 'day';
        $sqlDateSelector = '-7 day';
        $intervalMultiplier = 7;

        if ($timeInterval === 'month') {
            $sqlDateModifier = 'start of month';
            $sqlInterval = 'month';
            $sqlDateSelector = '-1 month';
            $intervalMultiplier = 1;
        }

        if ($timeInterval === 'year') {
            $sqlDateModifier = 'start of year';
            $sqlInterval = 'year';
            $sqlDateSelector = '-1 year';
            $intervalMultiplier = 1;
        }

        $modelWhere = 'WHERE project_id=:modelId';

        if ($model instanceof Client) {
            $modelWhere = 'WHERE project_id in (SELECT id FROM projects WHERE client_id=:modelId)';
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
                {$modelWhere}
                AND user_id=:userId
                GROUP BY dt
            )
            SELECT * FROM tally_range NATURAL LEFT OUTER JOIN tallies
        ";

        $result = DB::select(
            $query,
            [
                'range' => (abs($intervalCount) * $intervalMultiplier * -1) . ' ' . str_plural($sqlInterval),
                'modelId' => $model->getKey(),
                'userId' => $user->getKey(),
            ]
        );

        $result = collect($result)->reduce(function ($accumulator, $item) {
            $accumulator[$item->dt] = (int) $item->minutes;
            return $accumulator;
        }, []);

        return $result;
    }

    /**
     * Hints for autocomplete
     *
     * Returns an object whose values can be used to auto-populate
     * form fields during create or update.
     *
     * @param string $timezone The timezone to use with date values.
     *
     * @return object An object with suggested and previously-used values.
     */
    public function asSuggestion(string $timezone)
    {
        $now = Carbon::now();

        $suggestion = new stdClass();

        $suggestion->hideables = '';
        $suggestion->settables = ['billable' => true];

        if ($this->project->billable === false) {
            $suggestion->hideables .= 'billable';
            $suggestion->settables['billable'] = false;
        }

        $suggestion->previous = [
            'estimatedDuration' => $this->estimatedDuration,
            'start' => TimeHelper::date($timezone, $this->start),
            'summary' => $this->summary,
        ];

        $suggestion->suggested = [
            'estimatedDuration' => $this->estimatedDuration,
            'start' => TimeHelper::date($timezone, $now),
            'summary' => $this->summary,
        ];

        return $suggestion;
    }

    /**
     * Round start time to nearest 5-minute interval
     *
     * @param string|null $value The amount to be rounded.
     */
    public function setStartAttribute($value = null)
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
     * @param string $value A Carbon-compatible datetime string
     */
    public function getStartAttribute(string $value = null)
    {
        if (empty($value)) {
            return null;
        }

        $carbonInstance = new Carbon($value);
        return TimeHelper::roundToNearestMinuteMultiple($carbonInstance, 5);
    }

    /**
     * Round duration minutes to nearest 5-minute interval
     *
     * @param int|null $value The amount to be rounded.
     */
    public function setMinutesAttribute(int $value = null)
    {
        if ($value === null) {
            $this->attributes['minutes'] = 0;
            return;
        }
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

    /**
     * Mark a time entry as finished using the current time as the end point.
     */
    public function finish()
    {
        $this->end = new Carbon();
        $this->save();
    }
}
