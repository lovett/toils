<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract,
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
    protected $fillable = ['name', 'email', 'password'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    public function clients()
    {
        return $this->hasMany('App\Client');
    }

    public function projects()
    {
        return $this->hasMany('App\Project');
    }

    public function time()
    {
        return $this->hasMany('App\Time');
    }

    public function clientsForMenu()
    {
        $query = $this->clients()->orderBy('name');
        return $this->asMenu($query);
    }

    public function projectsForMenu()
    {
        $query = $this->projects()->orderBy('name');
        return $this->asMenu($query);
    }

    protected function asMenu($query, $key='id', $value='name')
    {
        $items = $query->get()->reduce(function ($acc, $item) use ($key, $value) {
            $acc[$item->$key] = $item->$value;
            return $acc;
        }, ['' => '']);

        return $items;
    }

}
