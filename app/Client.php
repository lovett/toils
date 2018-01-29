<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\Search;

/**
 * Eloquent model for the clients table.
 */
class Client extends Model
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
        'name' => 'clients.name',
        'status' => 'clients.active',
        'locality' => 'clients.locality',
        'created' => 'clients.created_at',
        'active' => 'clients.active',
    ];

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
     * Datatype mappings.
     *
     * @var array
     */
    protected $casts = [
        'active' => 'boolean',
        'totalTime' => 'integer'
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
     * @param Builder $builder The query to start with
     *
     * @return Relation
     */
    public static function listing(Builder $builder)
    {
        $builder = $builder->selectRaw(
            'clients.*,
            count(distinct projects.id) as projectCount,
            coalesce(sum(times.minutes), 0) as totalTime,
            max(times.start) as latestTime'
        );

        $builder = $builder->leftJoin(
            'projects',
            function ($join) {
                $join->on('clients.id', '=', 'projects.client_id')
                    ->where('projects.active', '=', 1)
                    ->whereNull('projects.deleted_at');
            }
        );

        $builder = $builder->leftJoin(
            'times',
            function ($join) {
                $join->on('times.project_id', '=', 'projects.id');
            }
        );

        $builder = $builder->orderBy('clients.updated_at', 'DESC');

        $builder = $builder->groupBy('clients.id');

        return $builder;
    }

    /**
     * Time entries associated with the client.
     *
     * @return HasManyThrough
     */
    public function times()
    {
        return $this->hasManyThrough('App\Time', 'App\Project');
    }

    /**
     * Users associated with the client.
     *
     * @return BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany('App\User');
    }

    /**
     * Projects associated with the client.
     *
     * @return HasMany
     */
    public function projects()
    {
        return $this->hasMany('App\Project');
    }

    /**
     * Invoices associated with the client.
     *
     * @return Builder
     */
    public function invoices()
    {
        return $this->hasManyThrough('App\Invoice', 'App\Project');
    }

    /**
     * Time entries associated with the client.
     *
     * @return Builder
     */
    public function time()
    {
        return $this->hasManyThrough('App\Time', 'App\Project');
    }

}
