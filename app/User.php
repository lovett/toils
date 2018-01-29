<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Traits\AsMenu;

class User extends Authenticatable
{
    use Notifiable, AsMenu;

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
     */
    public function client($id) {
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

        $query->where('client_user.user_id', $this->getKey());

        return $query;
    }

    /**
     * Single project associated with the user
     *
     */
    public function project($id) {
        return $this->projects()->where('id', $id)->firstOrFail();
    }

    /**
     * Single estimate associated with the user
     *
     */
    public function estimate($id) {
        return $this->estimates()->where('id', $id)->firstOrFail();
    }

    /**
     * Invoices associated with the user.
     *
     * @return Builder
     */
    public function invoices()
    {
        return Invoice::listingByUser($this->getKey());
    }

    /**
     * Single invoice associated with the user
     */
    public function invoice($id) {
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

    public function timeByProject($id)
    {
        return $this->time()->where('project_id', $id);
    }

    /**
     * Return a menu-friendly list of the user's clients.
     *
     * @return array
     */
    public function clientsForMenu()
    {
        $query = $this->clients()->orderBy('name')->getQuery();

        return $this->asMenu($query);
    }

    /**
     * Return a menu-friendly list of the user's projects across all clients
     *
     * @return array
     */
    public function projectsForMenu()
    {
        $query = $this->projects()->with('client')->orderBy('name');

        return $this->asMenu($query, 'id', ['name', 'client.name'], ' :: ');
    }

    /**
     * Return a menu-friendly list of the user's projects for a single client
     *
     * @return array
     */
    public function projectsByClientForMenu($clientId)
    {
        $query = $this->client($clientId)->projects()->getQuery();

        $query->with('client')->orderBy('name');

        return $this->asMenu($query, 'id', ['name', 'client.name'], ' :: ');
    }


}
