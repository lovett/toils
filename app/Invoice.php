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
 * Eloquent model for the invoices table.
 */
class Invoice extends Model
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
        'name' => 'invoices.name',
        'summary' => 'invoices.summary',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'number',
        'amount',
        'sent',
        'due',
        'paid',
        'name',
        'projet_id',
        'start',
        'end',
        'summary',
        'receiptType'
    ];

    /**
     * Datatype mappings.
     *
     * @var array
     */
    protected $casts = [];

    /**
     * The attributes that are datetimes.
     *
     * @var array
     */
    protected $dates = [
        'sent' => 'date',
        'due' => 'date',
        'paid' => 'date',
        'start' => 'date',
        'end' => 'date',
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
            'invoices.*,
            count(distinct projects.id) as projectCount,
            coalesce(sum(times.minutes), 0) as totalTime,
            max(times.start) as latestTime'
        );

        $builder = $builder->leftJoin(
            'projects',
            function ($join) {
                $join->on('invoices.project_id', '=', 'projects.id')
                    ->where('projects.active', '=', 1)
                    ->whereNull('projects.deleted_at');
            }
        );

        $builder = $builder->orderBy('invoices.updated_at', 'DESC');

        return $builder;
    }

    /**
     * Time entries entries associated with the invoice.
     *
     * @return HasManyThrough
     */
    public function times()
    {
        return $this->hasManyThrough('App\Time', 'App\Project');
    }

    /**
     * Users associated with the invoice.
     *
     * @return BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany('App\User');
    }

    /**
     * Projects associated with the invoice.
     *
     * @return HasMany
     */
    public function projects()
    {
        return $this->hasOne('App\Project');
    }

}
