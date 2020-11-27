<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\AsMenu;
use App\Traits\Search;

/**
 * Eloquent model for the user table.
 */
class User extends Authenticatable
{
    use Notifiable, AsMenu, SoftDeletes;

    /**
     * Default values for newly-created instances.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'timezone',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
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
     * Set instance defaults.
     *
     * @param array $attributes The key-value array to populate.
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    /**
     * Clients associated with the user.
     *
     * @return BelongsToMany
     */
    public function clients(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\Client');
    }

    /**
     * Estimates associated with the user
     *
     * @return BelongsToMany
     */
    public function estimates(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\Estimate');
    }

    /**
     * Single client associated with user
     *
     * @param integer $id A user primary key
     *
     * @return BelongsToMany
     */
    public function client(int $id): BelongsToMany
    {
        return $this->clients()->where('id', $id);
    }

    /**
     * Projects indirectly associated with the user through a client.
     *
     * Returns a Builder instance rather than a relation because of
     * the manual left join to client_user. A hasManyThrough relation
     * would not work because of the many-to-many relationship between
     * users and clients.
     *
     * @return Builder
     */
    public function projects(): Builder
    {
        $query = Project::leftJoin(
            'client_user',
            'client_user.client_id',
            '=',
            'projects.client_id'
        );

        $query = $query->where('client_user.user_id', $this->getKey());

        return $query;
    }

    /**
     * Single project associated with the user
     *
     * @param int $id A user primary key
     *
     * @return Builder
     */
    public function project(int $id): Builder
    {
        return $this->projects()->where('id', $id);
    }

    /**
     * Single estimate associated with the user
     *
     * @param int $id A user primary key
     *
     * @return Estimate
     */
    public function estimate(int $id): Estimate
    {
        return $this->estimates()->where('estimates.id', $id)->firstOrFail();
    }

    /**
     * Invoices associated with the user.
     *
     * @return Builder
     */
    public function invoices(): Builder
    {
        $query = Invoice::select('invoices.*');

        $query->leftJoin(
            'projects',
            'projects.id',
            '=',
            'invoices.project_id'
        );

        $query->leftJoin(
            'client_user',
            'client_user.client_id',
            '=',
            'projects.client_id'
        );

        $query->where('client_user.user_id', $this->getKey());

        return $query;
    }

    /**
     * Single invoice associated with the user
     *
     * @param integer $id An invoice primary key
     *
     * @return Model
     */
    public function invoice(int $id): Model
    {
        return $this->invoices()->where('invoices.id', $id)->with('project.client')->firstOrFail();
    }

    /**
     * Time entries associated with the user.
     *
     * @return HasMany
     */
    public function time(): HasMany
    {
        return $this->hasMany('App\Models\Time');
    }

    /**
     * Time entries associated with a project visible to the current user.
     *
     * @param integer $id A project primary key
     *
     * @return HasMany
     */
    public function timeByProject(int $id): HasMany
    {
        return $this->time()->where('project_id', $id);
    }

    /**
     * Return a menu-friendly list of the user's active clients.
     *
     * @return array
     */
    public function clientsForMenu(): array
    {
        $query = $this->clients()->active()->orderBy('name')->getQuery();

        return $this->asMenu($query);
    }

    /**
     * Return a menu-friendly list of the user's active projects
     *
     * @param int|null $clientId  A client primary key
     * @param int|null $projectId A project primary key to include even if inactive.
     *
     * @return array
     */
    public function projectsForMenu(int $clientId = null, $projectId = null): array
    {
        $query = $this->projects()->active()->orWhere('id', $projectId)->with('client')->orderBy('name');

        if ($clientId) {
            $query->where('projects.client_id', $clientId);
        }

        return $this->asMenu($query, 'id', ['name', 'client.name'], ' :: ');
    }
}
