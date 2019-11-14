<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use App\Traits\Search;
use App\Time;
use App\Client;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Helpers\TimeHelper;
use stdClass;
use Illuminate\Support\Facades\Storage;

/**
 * Eloquent model for the invoices table.
 */
class Invoice extends Model
{
    use SoftDeletes, Search;

    /**
     * Default values for newly-created instances.
     *
     * @var array
     */
    protected $attributes = [];


    /**
     * Fields that can be used for searching.
     *
     * Keys are field aliases suitable for presentation to user.
     * Values are qualified field names suitable for use in SQL queries.
     *
     * @var array
     */
    public static $searchables = [
        'number' => 'invoices.number',
        'name' => 'invoices.name',
        'summary' => 'invoices.summary',
        'client' => 'clientName',
        'project' => 'projectName',
        'sent' => 'invoices.sent',
    ];

    /**
     * Fields that can be used for filtering.
     *
     * @var array
     */
    public static $filterables = ['projectId'];

    /**
     * When an invoice is updated, mark its project updated as well.
     *
     * @var array
     */
    protected $touches = ['project'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'amount',
        'sent',
        'due',
        'paid',
        'name',
        'start',
        'end',
        'summary',
        'receiptType',
        'abandonned',
    ];

    /**
     * Datatype mappings.
     *
     * @var array
     */
    protected $casts = [
        'start' => 'datetime',
        'end' => 'datetime',
        'amount' => 'float',
        'number' => 'integer',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'deleted_at',
        'due',
        'end',
        'paid',
        'sent',
        'start',
        'updated_at',
        'abandonned',
    ];

    /**
     * Query scope to restrict a listing to a specific project.
     *
     * @param Builder $query     An existing query.
     * @param int     $projectId The primary key of a project.
     *
     * @return Builder;
     */
    public function scopeProject(Builder $query, int $projectId = 0)
    {
        return $query->where('projects.id', '=', $projectId);
    }

    /**
     * Query scope for narrowing to unpaid invoices
     *
     * @param Builder $query An existing query.
     *
     * @return Builder;
     */
    public function scopeUnpaid(Builder $query)
    {
        return $query->whereNull('paid');
    }

    /**
     * Query scope for narrowing to paid invoices
     *
     * @param Builder $query An existing query.
     *
     * @return QueryBuilder;
     */
    public function scopePaid(Builder $query)
    {
        return $query->whereNotNull('paid');
    }


    /**
     * Query scope to restrict by recentness
     *
     * @param Builder $query An existing query.
     * @param int     $limit If greater than zero, the max number of records to return.
     *
     * @return Builder;
     */
    public function scopeNewest(Builder $query, int $limit = 0)
    {
        $query->orderBy('end', 'DESC');
        if ($limit > 0) {
            $query->limit($limit);
        }
        return $query;
    }

    /**
     * Master query for getting a list of records.
     *
     * @param Builder $builder The query to start with.
     *
     * @return QueryBuilder;
     */
    public static function listing(Builder $builder)
    {
        $joins = ($builder->getQuery()->joins) ?: [];
        $joinedTables = array_map(function ($join) {
            return $join->table;
        }, $joins);

        $builder = $builder->selectRaw('invoices.*');
        $builder = $builder->selectRaw('SUM(times.minutes) as totalMinutes');

        $builder = $builder->leftJoin(
            'times',
            'invoices.id',
            '=',
            'times.invoice_id'
        );

        if (in_array('projects', $joinedTables) === false) {
            $builder = $builder->join('projects', 'times.project_id', '=', 'projects.id');
        }

        $builder = $builder->leftJoin(
            'clients',
            'projects.client_id',
            '=',
            'clients.id'
        );

        $builder = $builder->addSelect('clients.id as client_id');
        $builder = $builder->addSelect('clients.name as clientName');
        $builder = $builder->addSelect('projects.name as projectName');
        $builder = $builder->addSelect('projects.id as project_id');
        $builder = $builder->whereNull('times.deleted_at');
        $builder = $builder->groupBy('invoices.id');
        $builder = $builder->orderByRaw('invoices.paid is null desc, invoices.due desc');

        return $builder;
    }

    /**
     * Set instance defaults.
     *
     * The start date defaults to 30 days in the past. The end date is
     * 30 days in the future but adjusted upward to avoid falling on a
     * weekend.
     *
     * @param array $attributes The key-value array to populate.
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $now = new Carbon();

        $this->attributes = array_merge(
            $this->attributes,
            [
                'sent' => $now,
                'due' => $now->copy()->addDays(30),
                'amount' => 0,
            ],
        );

        while ($this->due->isWeekend()) {
            $this->due = $this->due->addDays(1);
        }
    }


    /**
     * Time entries entries associated with the invoice.
     *
     * @return HasMany
     */
    public function time()
    {
        return $this->hasMany('App\Time');
    }

    /**
     * Project associated with the invoice.
     *
     * @return BelongsTo
     */
    public function project()
    {
        return $this->belongsTo('App\Project');
    }

    /**
     * Custom attribute for de-nesting the client relation
     *
     * An invoice is indirectly related to a client through a project.
     * The invoice should already have eager-loaded the project relation.
     *
     * @return Client The client associated with this invoice's project
     */
    public function getClientAttribute()
    {
        return $this->project->client;
    }

    /**
     * Custom attribute for treating payment date field as a boolean
     */
    public function getIsPaidAttribute()
    {
        return $this->paid !== null;
    }

    /**
     * Custom attribute for calculating days remaining until due
     */
    public function getDaysUntilDueAttribute()
    {
        if ($this->isPaid) {
            return 0;
        }

        $now = new Carbon();
        return $now->diffInDays($this->due, false);
    }

    /**
     * Hints for autocomplete
     *
     * Returns an object whose values can be used to auto-populate
     * form fields during create or update.
     *
     * @return object An object with suggested and previously-used values.
     */
    public function getSuggestionAttribute()
    {
        $now = Carbon::now();

        $suggestion = new stdClass();

        $suggestion->previous = [
            'amount' => $this->amount,
            'name' => $this->name,
            'summary' => $this->summary,
            'start' => TimeHelper::date($this->start),
            'end' => TimeHelper::date($this->end),
        ];

        $suggestion->suggested = [
            'amount' => $this->amount,
            'name' => $this->name,
            'summary' => $this->summary,
            'end' => TimeHelper::date($now),
            // Suggested start is the next day after the previous end.
            'start' => TimeHelper::date($this->end->addDay()),
        ];

        return $suggestion;
    }

    /**
     * Set the date paid when the receipt is set
     *
     * @param string $value The receipt
     */
    public function setReceiptAttribute(?string $value = null)
    {
        $this->attributes['receipt'] = $value;
        $this->attributes['paid'] = ($value === null) ? null : new Carbon();
    }

    /**
     * Move a previously-uploaded receipt to the trash
     *
     * @return void
     */
    public function trashReceipt()
    {
        $path = $this->receipt;

        if (empty($path)) {
            return;
        }

        $trashPath = sprintf('trash/%s', $path);
        Storage::move($path, $trashPath);
    }
}
