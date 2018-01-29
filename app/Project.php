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
