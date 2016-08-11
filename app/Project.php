<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Eloquent model for the projects table
 */
class Project extends Model
{
    use SoftDeletes;

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
     * Mapping between attributes and data types
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
        $relation = $relation->with('client');
        $relation = $relation->orderByRaw('LOWER(projects.name) ASC');
        return $relation;
    }

    /**
     * User associated with the project
     *
     * @return BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany('App\User');
    }

    /**
     * Client associated with the project
     *
     * @return HasOne
     */
    public function client()
    {
        return $this->belongsTo('App\Client');
    }


    /**
     * Time entires associated with the project
     *
     * @return HasMany
     */
    public function time()
    {
        return $this->hasMany('App\Time');
    }

    /**
     * Human-readable value for active boolean fied
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
     * Human-readable value for taxDeducted boolean field
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
     * Human-readable value for billable boolean field
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
