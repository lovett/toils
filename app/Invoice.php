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
use Carbon\Carbon;

/**
 * Eloquent model for the invoices table.
 */
class Invoice extends Model
{
    use SoftDeletes, Search;

    /**
     * The model's attributes.
     *
     * @var array
     */
    protected $attributes = [];


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
        'sent' => 'datetime',
        'due' => 'datetime',
        'paid' => 'datetime',
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
        $builder->select([
            'invoices.*',
            'clients.name as clientName',
            'clients.id as clientId',
            'projects.name as projectName',
            'projects.id as projectId',
        ]);
        $builder->leftJoin(
            'projects',
            'invoices.project_id',
            '=',
            'projects.id'
        );
        $builder->whereNull('projects.deleted_at');

        $builder->join(
            'clients',
            'projects.client_id',
            '=',
            'clients.id'
        );
        $builder->whereNull('clients.deleted_at');

        $builder->join(
            'client_user',
            'client_user.client_id',
            '=',
            'clients.id'
        );

        $builder = $builder->orderBy('invoices.sent', 'DESC');
        $builder->selectRaw('SUM(times.minutes) as totalMinutes');

        $builder->leftJoin(
            'times',
            'invoices.id',
            '=',
            'times.invoice_id'
        );
        $builder->whereNull('times.deleted_at');

        $builder = $builder->groupBy('invoices.id');

        return $builder;
    }

    public function __construct()
    {
        $now = new Carbon();

        $this->attributes = [
            'sent' => $now,
            'start' => $now,
            'end' => $now,
            'due' => $now->copy()->addDays(30),
            'amount' => 0,
        ];
        parent::__construct();
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
