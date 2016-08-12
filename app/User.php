<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Relations\Relation\HasMany;

/**
 * Eloquent model for the users table
 */
class User extends Model implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword;

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
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];


    /**
     * Clients associated with the user
     *
     * @return BelongsToMany
     */
    public function clients()
    {
        return $this->belongsToMany('App\Client');
    }

    /**
     * Projects associated with the user
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
     * Time entries associated with the user
     *
     * @return HasMany
     */
    public function time()
    {
        return $this->hasMany('App\Time');
    }

    /**
     * Return a menu-friendly list of the user's clients
     *
     * @return array
     */
    public function clientsForMenu()
    {
        $query = $this->clients()->orderBy('name');
        return $this->asMenu($query);
    }

    /**
     * Return a menu-friendly list of the user's projects
     *
     * @return array
     */
    public function projectsForMenu()
    {
        $query = $this->projects()->orderBy('name');
        return $this->asMenu($query);
    }

    /**
     * Return a list of key-value pairs suitable for display in an HTML menu
     *
     * @param Relation $query Determines the list being returned.
     * @param string   $key   The field name in query to use as the key.
     * @param string   $value The field name in query to use as the value.
     *
     * @return array
     */
    protected function asMenu(Relation $query, $key = 'id', $value = 'name')
    {
        $items = $query->get()->reduce(
            function ($acc, $item) use ($key, $value) {
                $acc[$item->$key] = $item->$value;
                return $acc;
            },
            ['' => '']
        );

        return $items;
    }
}
