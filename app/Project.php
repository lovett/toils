<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Prune;
use App\Traits\Search;
use Carbon\Carbon;
use Carbon\CarbonInterface;

/**
 * Eloquent model for the projects table.
 */
class Project extends Model
{
    use SoftDeletes, Search, Prune;

    /**
     * Default values for newly-created instances.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Fields that can be used for searching.
     *
     * Keys are field aliases suitable for use in UI.
     * Values are qualified field names suitable for use in SQL queries.
     *
     * @var array
     */
    public static $searchables = [
        'name' => 'projects.name',
        'client' => 'clientName',
        'status' => 'projects.active',
    ];

    /**
     * Alternate values that should be mapped to incoming search keywords.
     *
     * @var array
     */
    public static $searchAliases = [
        'projects.active' => [
            'active' => true,
            'inactive' => false,
        ],
    ];


    /**
     * When a project is updated, mark its client as updated as well.
     *
     * @var array
     */
    protected $touches = ['client'];

    /**
     * The attributes that are mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'active',
        'name',
        'billable',
        'taxDeducted',
        'client_id',
        'allottedTotalMinutes',
        'allottedWeeklyMinutes',
        'allottedTotalHours',
        'allottedWeeklyHours',
    ];

    /**
     * Mapping between attributes and data types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'active' => 'boolean',
        'name' => 'string',
        'billable' => 'boolean',
        'taxDeducted' => 'boolean',
        'user_id' => 'integer',
        'client_id' => 'integer',
        'allottedTotalMinutes' => 'integer',
        'allottedWeeklyMinutes' => 'integer',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'deleted_at',
        'updated_at',
        'lastActive',
    ];

    /**
     * Set instance defaults.
     *
     * @param array $attributes The key-value array to populate.
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    /**
     * Custom accessor for allottedTotalMinutes specified in hours
     *
     * Although the database stores minutes, sometimes it is more
     * convenient to use hours.
     */
    public function getAllottedTotalHoursAttribute()
    {
        if ($this->allottedTotalMinutes === null) {
            return null;
        }

        return round($this->allottedTotalMinutes / 60, 2);
    }

    /**
     * Custom mutator for allottedTotalMinutes specified in hours
     *
     * For occasions when it's more convenient to deal with hours
     * rather than minutes.
     *
     * @param float $value The number of hours to be converted to minutes
     */
    public function setAllottedTotalHoursAttribute(float $value = null)
    {
        if ($value !== null) {
            $value = round($value * 60);
        }

        $this->attributes['allottedTotalMinutes'] = $value;
    }

    /**
     * Custom accessor for allottedWeeklyMinutes specified in hours
     *
     * Although the database stores minutes, sometimes it is more
     * convenient to use hours.
     *
     * @return float|null Total minutes expressed as hours to 2 decimal places.
     */
    public function getAllottedWeeklyHoursAttribute()
    {
        if ($this->allottedWeeklyMinutes === null) {
            return null;
        }

        return round($this->allottedWeeklyMinutes / 60, 2);
    }

    /**
     * Custom mutator for allottedWeeklyMinutes specified in hours
     *
     * For occasions when it's more convenient to deal with hours
     * rather than minutes.
     *
     * @param float $value The number of hours to be converted to minutes
     */
    public function setAllottedWeeklyHoursAttribute(float $value = null)
    {
        if ($value !== null) {
            $value = round($value * 60);
        }

        $this->attributes['allottedWeeklyMinutes'] = $value;
    }

    /**
     * Computed accessor for how much billable time is left on the project
     *
     * @return int|null A number in minutes, or null if no budget has been set.
     */
    public function getTotalTimeRemainingAttribute()
    {
        if ($this->allottedTotalMinutes === null) {
            return null;
        }

        $totalTime = $this->time()->sum('minutes');

        $remaining = $this->allottedTotalMinutes - $totalTime;

        return $remaining;
    }

    /**
     * Computed accessor for how much billable time is left this week
     *
     * @return int|null A number in minutes, or null if no weekly budget is set.
     */
    public function getWeeklyTimeRemainingAttribute()
    {
        if ($this->allottedWeeklyMinutes === null) {
            return null;
        }

        $timeThisWeek = $this->time()->thisWeek()->sum('minutes');

        $remaining = $this->allottedWeeklyMinutes - $timeThisWeek;

        return $remaining;
    }

    /**
     * Master query for getting a list of records.
     *
     * @param Builder $query The query to start with
     *
     * @return Builder
     */
    public static function listing(Builder $query)
    {
        $query->selectRaw(
            'projects.*,
            clients.name as clientName,
            sum(case when times.invoice_id is null then minutes else 0 end) as unbilledTime,
            sum(case when times.invoice_id is null then 0 else minutes end) as billedTime,
            coalesce(max(times.start), 0) as lastActive'
        );

        $query->leftJoin(
            'clients',
            function ($join) {
                $join->on('projects.client_id', '=', 'clients.id');
            }
        );

        $query->leftJoin(
            'times',
            function ($join) {
                $join->on('times.project_id', '=', 'projects.id');
            }
        );

        $query->groupBy('projects.id');

        $query = $query->orderByRaw('LOWER(projects.name) ASC');

        return $query->with('client');
    }

    /**
     * At-a-glance numbers that summarize a project in various ways.
     */
    public function stats()
    {
        $stats = [];
        $stats['paid'] = $this->invoices()->paid()->sum('amount');
        $stats['unpaid'] = $this->invoices()->unpaid()->sum('amount');
        $stats['income'] = $stats['paid'] + $stats['unpaid'];

        $stats['start'] = null;
        $stats['end'] = null;
        $stats['age'] = null;
        $start = $this->time()->min('times.start');
        if ($start) {
            $stats['start'] = new Carbon($start);
            $stats['age'] = Carbon::now()->diffForHumans(
                $stats['start'],
                CarbonInterface::DIFF_ABSOLUTE
            );
        }

        if ((bool) $this->active === false) {
            $end = $this->time()->max('times.start');
            $stats['end'] = new Carbon($end);
            $stats['duration'] = $stats['end']->diffForHumans(
                $stats['start'],
                CarbonInterface::DIFF_ABSOLUTE
            );
        }

        $stats['billable_minutes'] = $this->time()->billable()->sum('minutes');
        $stats['unbillable_minutes'] = $this->time()->unbillable()->sum('minutes');
        $stats['total_minutes'] = $stats['billable_minutes'] + $stats['unbillable_minutes'];

        $stats['hourly_rate'] = null;
        if ($stats['total_minutes'] > 1) {
            $stats['hourly_rate'] = round($stats['income'] / $stats['total_minutes'] * 60);
        }

        return $stats;
    }

    /**
     * User associated with the project.
     *
     * @return BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany('App\User');
    }

    /**
     * Client associated with the project.
     *
     * @return BelongsTo
     */
    public function client()
    {
        return $this->belongsTo('App\Client');
    }

    /**
     * Time entries associated with the project.
     *
     * @return HasMany
     */
    public function time()
    {
        return $this->hasMany('App\Time');
    }

    /**
     * Invoices associated with the project.
     *
     * @return HasMany
     */
    public function invoices()
    {
        return $this->hasMany('App\Invoice');
    }

    /**
     * Query scope for narrowing to active projects
     *
     * @param Builder $query An existing query.
     *
     * @return Builder
     */
    public function scopeActive(Builder $query)
    {
        return $query->where('projects.active', true);
    }

    /**
     * Query scope for narrowing to inactive projects
     *
     * @param Builder $query An existing query.
     *
     * @return Builder
     */
    public function scopeInActive(Builder $query)
    {
        return $query->where('projects.active', false);
    }

    /**
     * Query scope to restrict by recentness
     *
     * @param Builder $query An existing query.
     * @param int     $limit If greater than zero, the max number of records to return.
     *
     * @return Builder;
     */
    public function scopeNewest(Builder $query, int $limit = 0)
    {
        $query->orderBy('projects.updated_at', 'DESC');
        if ($limit > 0) {
            $query->limit($limit);
        }
        return $query;
    }
}
