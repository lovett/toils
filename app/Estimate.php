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
     * The attributes that are dates.
     *
     * @var array
     */
    protected $dates = [
        'submitted',
        'closed',
        'created_at',
        'updated_at',
        'deleted_at',
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
}
