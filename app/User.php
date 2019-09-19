<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\AsMenu;

/**
 * Eloquent model for the user table.
 */
class User extends Authenticatable
{
    use Notifiable, AsMenu;

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
    public function clients()
    {
        return $this->belongsToMany('App\Client');
    }

    /**
     * Estimates associated with the user
     *
     * @return BelongsToMany
     */
    public function estimates()
    {
        return $this->belongsToMany('App\Estimate');
    }

    /**
     * Single client associated with user
     *
     * @param integer $id A user primary key
     */
    public function client(int $id)
    {
        return $this->clients()->where('id', $id)->firstOrFail();
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
    public function projects()
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
     */
    public function project(int $id)
    {
        return $this->projects()->where('id', $id);
    }

    /**
     * Single estimate associated with the user
     *
     * @param int $id A user primary key
     */
    public function estimate(int $id)
    {
        return $this->estimates()->where('estimates.id', $id)->firstOrFail();
    }

    /**
     * Invoices associated with the user.
     *
     * @return Builder
     */
    public function invoices()
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
     */
    public function invoice(int $id)
    {
        return $this->invoices()->where('invoices.id', $id)->with('project.client')->firstOrFail();
    }

    /**
     * Time entries associated with the user.
     *
     * @return HasMany
     */
    public function time()
    {
        return $this->hasMany('App\Time');
    }

    /**
     * Time entries associated with a project visible to the current user.
     *
     * @param integer $id A project primary key
     */
    public function timeByProject(int $id)
    {
        return $this->time()->where('project_id', $id);
    }

    /**
     * Return a menu-friendly list of the user's active clients.
     *
     * @return array
     */
    public function clientsForMenu()
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
    public function projectsForMenu(int $clientId = null, $projectId = null)
    {
        $query = $this->projects()->active()->orWhere('id', $projectId)->with('client')->orderBy('name');

        if ($clientId) {
            $query->where('projects.client_id', $clientId);
        }

        return $this->asMenu($query, 'id', ['name', 'client.name'], ' :: ');
    }
}
