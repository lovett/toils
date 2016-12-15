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
use App\Time;
use Illuminate\Support\Facades\DB;

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
    protected $casts = [
        'start' => 'datetime',
        'end' => 'datetime',
    ];

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
        $builder = $builder->orderBy('invoices.sent', 'DESC');

        $builder->with('project.client');
        return $builder;
    }

    /**
     * Time entries entries associated with the invoice.
     *
     * @return HasMany
     */
    public function times()
    {
        return $this->hasMany('App\Time');
    }

    /**
     * Project associated with the invoice.
     *
     * @return HasOne
     */
    public function project()
    {
        return $this->belongsTo('App\Project');
    }

    /**
     * Client associated with the invoice.
     *
     * @return HasOne
     */
    public function client()
    {
        return $this->project()->with('client');
    }

    public function attachTime()
    {
        DB::beginTransaction();

        $this->times()->update(['invoice_id' => null]);

        $times = Time::whereBetween('start', [$this->start, $this->end]);
        $times->where('project_id', $this->project_id);
        $times->whereNull('invoice_id');

        $times->update(['invoice_id' => $this->getKey()]);

        DB::commit();
    }
}
