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
use App\Client;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Helpers\TimeHelper;
use stdClass;

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
        'name' => 'invoices.name',
        'summary' => 'invoices.summary',
    ];

    /**
     * Fields that can be used for filtering.
     */
    public static $filterables = [
        'projectId',
    ];

    /**
     * When an invoice is updated, mark its project updated as well.
     *
     * @var array
     */
    protected $touches = [
        'project',
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
        'amount' => 'float',
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
    ];

    /**
     * Query scope to restrict a listing to a specific project.
     *
     * @param Builder $query An existing query.
     * @param int $projectId The primary key of a project.
     *
     * @return Builder;
     */
    public function scopeProject($query, $projectId=0)
    {
        return $query->where('projects.id', '=', $projectId);
    }

    /**
     * Query scope to restrict by record age
     *
     * @param Builder $query An existing query.
     * @param int $limit If greater than zero, the max number of records to return.
     *
     * @return Builder;
     */
    public function scopeRecent($query, $limit=0)
    {
        $query->orderBy('sent', 'DESC');
        if ($limit > 0) {
            $query->limit($limit);
        }
        return $query;
    }

    /**
     * Query scope for
     *
     * @param Builder $builder The query to start with
     *
     * @return Relation
     */
    public function scopeListingByUser($query)
    {
        $query->leftJoin(
            'projects',
            'invoices.project_id',
            '=',
            'projects.id'
        );

        $query->whereNull('projects.deleted_at');

        $query->join(
            'clients',
            'projects.client_id',
            '=',
            'clients.id'
        );

        $query->whereNull('clients.deleted_at');

        $query->join(
            'client_user',
            'client_user.client_id',
            '=',
            'clients.id'
        );

        $query->select([
            'invoices.*',
            'clients.name as clientName',
            'clients.id as clientId',
            'projects.name as projectName',
            'projects.id as projectId',
        ]);

        $query->selectRaw('SUM(times.minutes) as totalMinutes');

        $query->leftJoin(
            'times',
            'invoices.id',
            '=',
            'times.invoice_id'
        );

        $query->whereNull('times.deleted_at');

        $query->groupBy('invoices.id');

        return $query;
    }

    public function __construct(array $attributes=[])
    {

        $now = new Carbon();
        $this->attributes = [
            'sent' => $now,
            'start' => $now->copy()->subDays(30),
            'end' => $now,
            'due' => $now->copy()->addDays(30),
            'amount' => 0,
        ];

        parent::__construct($attributes);
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
     * Custom attribute for de-nesting the client relation
     *
     * An invoice is indirectly related to a client through a project.
     * The invoice should already have eager-loaded the project relation.
     *
     * @return Client The client associated with this invoice's project
     */
    public function getClientAttribute() {
        return $this->project->client;
    }

    /**
     * Custom attribute for treating payment date field as a boolean
     */
    public function getIsPaidAttribute() {
        return $this->paid !== null;
    }

    /**
     * Custom attribute for calculating days remaining until due
     */
    public function getDaysUntilDueAttribute() {
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
     */
    public function setReceiptAttribute($value)
    {
        $this->attributes['receipt'] = $value;
        $this->attributes['paid'] = ($value === null)? null : new Carbon();
    }

    /**
     * Round the start attribute to the beginning of the day
     */
    public function setStartAttribute(string $value = null)
    {
        if (!empty($value)) {
            $value = (new Carbon($value))->startOfDay();
        }

        $this->attributes['start'] = $value;
    }

    /**
     * Round the end attribute to the end of the day
     */
    public function setEndAttribute(string $value = null)
    {
        if (!empty($value)) {
            $value = (new Carbon($value))->endOfDay();
        }

        $this->attributes['end'] = $value;
    }
}
