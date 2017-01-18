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
use App\Helpers\TimeHelper;

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
        'amount' => 'float',
    ];

    /**
     * The attributes that are datetimes.
     *
     * @var arrayn
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'sent',
        'due',
        'paid',
        'start',
        'end',
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

    public function scopeNewest($query)
    {
        $query->orderBy('sent', 'DESC');
    }

    /**
     * Query scope for
     *
     * @param Builder $builder The query to start with
     *
     * @return Relation
     */
    public function scopeListing($query, $userId=0)
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

        if ($userId > 0) {
            $query->join(
                'client_user',
                'client_user.client_id',
                '=',
                'clients.id'
            );
        }

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

    public function __construct()
    {
        $now = new Carbon();

        $this->attributes = [
            'sent' => $now,
            'start' => $now->copy()->subDays(30),
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

    /**
     * Create a subset representation for providing autocompletion hints.
     *
     * Returns an object whose fields reflect the $suggestable
     * property, both to limit what fields are exposed and to allow
     * their values to be customized.
     *
     * @return object An object with suggested and previously-used values.
     */
    public function toSuggestion()
    {
        $now = Carbon::now();

        $suggestion = [
            'previous' => [
                'amount' => $this->amount,
                'name' => $this->name,
                'summary' => $this->summary,
                'start' => TimeHelper::date($this->start),
                'end' => TimeHelper::date($this->end),
            ],
            'suggested' => [
                'amount' => $this->amount,
                'name' => $this->name,
                'summary' => $this->summary,
                'end' => TimeHelper::dateField($now),
                // Suggested start is the next day after the previous end.
                'start' => TimeHelper::dateField($this->end->addDay()),
            ],
        ];

        return (object) $suggestion;
    }
}
