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
    public static $searchables = ['name' => 'projects.name'];

    /**
     * The attributes that are mass assignable.
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
     * @param Builder $query The query to start with
     *
     * @return Builder
     */
    public static function listing(Builder $query)
    {
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
     * Time entires associated with the project.
     *
     * @return HasMany
     */
    public function time()
    {
        return $this->hasMany('App\Time');
    }

    /**
     * Human-readable value for active boolean fied.
     *
     * @return string
     */
    public function status()
    {
        if ($this->active) {
            return 'active';
        }

        return 'inactive';
    }

    /**
     * Human-readable value for taxDeducted boolean field.
     *
     * @return string
     */
    public function taxStatus()
    {
        if ($this->taxDeducted) {
            return 'deducted';
        }

        return 'not deducted';
    }

    /**
     * Human-readable value for billable boolean field.
     *
     * @return string
     */
    public function billableStatus()
    {
        if ($this->billable) {
            return 'yes';
        }

        return 'no';
    }
}
