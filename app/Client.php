<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Eloquent model for the clients table
 */
class Client extends Model
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
        'contactName',
        'contactEmail',
        'address1',
        'address2',
        'city',
        'locality',
        'postalCode',
        'phone',
    ];

    /**
     * The attributes that are datetimes
     *
     * @var array
     */
    protected $dates = ['deleted_at' => 'datetime'];


    /**
     * Master query for getting a list of records
     *
     * @param Relation $relation The relation to start with.
     *
     * @return Relation
     */
    public static function listing(BelongsToMany $relation)
    {
        $relation = $relation->selectRaw(
            'clients.*,
            count(distinct projects.id) as projectCount,
            sum(times.minutes) as totalTime,
            max(times.start) as latestTime'
        );

        $relation = $relation->leftJoin(
            'projects',
            function ($join) {
                $join->on('clients.id', '=', 'projects.client_id')
                    ->where('projects.active', '=', 1)
                    ->whereNull('projects.deleted_at');
            }
        );

        $relation = $relation->leftJoin(
            'times',
            function ($join) {
                $join->on('times.project_id', '=', 'projects.id');
            }
        );

        $relation = $relation->orderBy('clients.updated_at', 'DESC');
        $relation = $relation->groupBy('clients.id');
        return $relation;
    }

    /**
     * Time entries associated with the client
     *
     * @return HasManyThrough
     */
    public function times()
    {
        return $this->hasManyThrough('App\Time', 'App\Project');
    }

    /**
     * Users associated with the client
     *
     * @return BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany('App\User');
    }

    /**
     * Projects associated with the client
     *
     * @return HasMany
     */
    public function projects()
    {
        return $this->hasMany('App\Project');
    }

    /**
     * Human readable string for the value of the active flag
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
}
