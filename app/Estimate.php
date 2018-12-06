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
 * Eloquent model for the estimates table.
 */
class Estimate extends Model
{
    use SoftDeletes, Search;

    public $statuses = [
        null => '',
        'waiting'  => 'Waiting',
        'accepted' => 'Accepted',
        'declined' => 'Declined',
        'abandonned' => 'Abandonned',
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
        'name' => 'estimates.name',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'submitted',
        'closed',
        'recipient',
        'client_id',
        'fee',
        'hours',
        'summary',
        'status',
    ];

    /**
     * Datatype mappings.
     *
     * @var array
     */
    protected $casts = [
        'hours' => 'integer',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'closed',
        'created_at',
        'deleted_at',
        'submitted',
        'updated_at',
    ];

    /**
     * Master query for getting a list of records.
     *
     * @param Builder $query The query to start with
     *
     * @return Relation
     */
    public static function listing(Builder $query)
    {
        return $query->orderByRaw('LOWER(estimates.name) ASC');
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
     * Project associated with the client.
     *
     * @return HasOne
     */
    public function project()
    {
        return $this->hasOne('App\Project');
    }

    /**
     * Client associated with the client.
     *
     * @return HasOne
     */
    public function client()
    {
        return $this->hasOne('App\Client');
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
        $query->orderBy('submitted', 'desc');

        if ($limit > 0) {
            $query->limit($limit);
        }
        return $query;
    }

    /**
     * Query scope for presenting a list of records
     *
     * Adds extra fields to the select clause to provide additional
     * context when the query will be rendered in a table as a list.
     *
     * Considers the joins property of the query to avoid redundant
     * joins. The caller is still responsible for eager loading.
     *
     * @param Builder $query An existing query.
     *
     * @return Builder;
     */
    public function scopeForList($query)
    {
        $query->select('estimates.*');
        $query->leftJoin('clients', 'estimates.client_id', '=', 'clients.id');
        $query->selectRaw('clients.name as clientName');
        $query->selectRaw('clients.id as clientId');

        return $query;
    }

}
