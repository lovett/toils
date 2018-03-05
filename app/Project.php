<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\Search;

/**
 * Eloquent model for the projects table.
 */
class Project extends Model
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
        'name' => 'projects.name',
    ];


    /**
     * When a project is updated, mark its client as updated as well.
     *
     * @var array
     */
     protected $touches = [
         'client',
     ];

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
    ];

    /**
     * Computed accessor for a human-readable version of taxDeducted boolean.
     *
     * @return string
     */
    public function getTaxStatusAttribute()
    {
        return ($this->taxDeducted) ? 'deducted' : 'not deducted';
    }

    /**
     * Computed accessor for a Human-readable version of billable boolean.
     *
     * @return string
     */
    public function getBillableStatusAttribute()
    {
        return ($this->billable) ? 'yes' : 'no';
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
            coalesce(sum(times.minutes), 0) as unbilledTime'
        );

        $query->leftJoin(
            'times',
            function ($join) {
                $join->on('times.project_id', '=', 'projects.id');
                $join->whereNull('times.invoice_id');
            }
        );

        $query->groupBy('projects.id');

        return $query->with('client')->orderByRaw('LOWER(projects.name) ASC');
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
     * @return HasOne
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
     * @return Builder;
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Query scope for narrowing to inactive projects
     *
     * @param Builder $query An existing query.
     *
     * @return Builder;
     */
    public function scopeInActive($query)
    {
        return $query->where('active', false);
    }

    /**
     * Query scope to restrict by recentness
     *
     * @param Builder $query An existing query.
     * @param int $limit If greater than zero, the max number of records to return.
     *
     * @return Builder;
     */
    public function scopeNewest($query, $limit=0)
    {
        $query->orderBy('updated_at', 'DESC');
        if ($limit > 0) {
            $query->limit($limit);
        }
        return $query;
    }


    public function scopeUnbilledTime($query)
    {
        $query->with('time');
    }
}
